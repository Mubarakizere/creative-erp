<?php

namespace App\Services\Asset;

use App\Models\Asset;
use App\Models\AssetDisposal;
use App\Models\AssetImpairment;
use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Services\Finance\JournalService;
use Exception;

class AssetAccountingService
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function postCapitalization(Asset $asset, float $amount, ?string $memo = null): ?Journal
    {
        if ($amount <= 0) {
            return null;
        }

        $assetAccount = $asset->category->assetAccount;
        if (!$assetAccount) {
            throw new Exception("Asset category must have an asset account configured for capitalization.");
        }

        // We assume capitalization offset is a general clearing account or AP if it comes from invoice.
        // The exact offset is determined by the source (e.g. Purchase Invoice clearing account).
        // Since the prompt doesn't specify the exact AP flow for capitalization, we debit the asset account
        // and credit a clearing account or payable, but the most important part is getting the Asset on the books.
        
        // For manual capitalization:
        $clearingAccount = ChartOfAccount::where('company_id', $asset->company_id)
            ->where('name', 'like', '%Clearing%')
            ->first();

        if (!$clearingAccount) {
             throw new Exception("Clearing account not found for capitalization.");
        }

        $entries = [
            [
                'chart_of_account_id' => $assetAccount->id,
                'debit' => $amount,
                'credit' => 0,
                'branch_id' => $asset->branch_id,
                'department_id' => $asset->department_id,
                'project_id' => $asset->project_id,
            ],
            [
                'chart_of_account_id' => $clearingAccount->id,
                'debit' => 0,
                'credit' => $amount,
                'branch_id' => $asset->branch_id,
                'department_id' => $asset->department_id,
                'project_id' => $asset->project_id,
            ]
        ];

        return $this->journalService->createAutomaticJournal([
            'company_id' => $asset->company_id,
            'branch_id' => $asset->branch_id,
            'department_id' => $asset->department_id,
            'project_id' => $asset->project_id,
            'date' => $asset->in_service_date ?? $asset->purchase_date ?? now()->toDateString(),
            'memo' => $memo ?? "Capitalization of Asset: {$asset->asset_number}",
            'reference_number' => 'CAP-' . $asset->asset_number,
        ], $entries);
    }

    public function postImpairment(AssetImpairment $impairment): Journal
    {
        $asset = $impairment->asset;
        $category = $asset->category;

        $accumulatedDepreciationAccount = $category->accumulatedDepreciationAccount;
        if (!$accumulatedDepreciationAccount) {
            throw new Exception("Accumulated depreciation account is missing for category {$category->name}");
        }

        // Impairment loss usually debits Impairment Loss Expense and credits Accumulated Depreciation / Accumulated Impairment
        $impairmentExpenseAccount = ChartOfAccount::where('company_id', $asset->company_id)
            ->where('name', 'like', '%Impairment Loss%')
            ->first();

        if (!$impairmentExpenseAccount) {
             throw new Exception("Impairment Loss expense account not found.");
        }

        $entries = [
            [
                'chart_of_account_id' => $impairmentExpenseAccount->id,
                'debit' => $impairment->amount,
                'credit' => 0,
            ],
            [
                'chart_of_account_id' => $accumulatedDepreciationAccount->id,
                'debit' => 0,
                'credit' => $impairment->amount,
            ]
        ];

        $journal = $this->journalService->createAutomaticJournal([
            'company_id' => $asset->company_id,
            'date' => $impairment->date->toDateString(),
            'memo' => "Impairment of Asset: {$asset->asset_number}. Reason: {$impairment->reason}",
            'reference_number' => 'IMP-' . $asset->asset_number . '-' . $impairment->id,
        ], $entries);

        $impairment->update(['journal_id' => $journal->id, 'status' => 'Approved']);
        
        $asset->current_book_value -= $impairment->amount;
        $asset->save();

        return $journal;
    }

    public function postDisposal(AssetDisposal $disposal): Journal
    {
        $asset = $disposal->asset;
        $category = $asset->category;

        $assetAccount = $category->assetAccount;
        $accDepAccount = $category->accumulatedDepreciationAccount;

        if (!$assetAccount || !$accDepAccount) {
            throw new Exception("Asset or Accumulated Depreciation account is missing for category {$category->name}");
        }

        $gainLossAccount = ChartOfAccount::where('company_id', $asset->company_id)
            ->where('name', 'like', '%Gain/Loss on Disposal%')
            ->first() ?? ChartOfAccount::where('company_id', $asset->company_id)
            ->where('name', 'like', '%Disposal%')
            ->first();

        if (!$gainLossAccount) {
             throw new Exception("Gain/Loss on Disposal account not found.");
        }

        $cashAccount = null;
        if ($disposal->sale_price > 0) {
            $cashAccount = ChartOfAccount::where('company_id', $asset->company_id)
                ->where('name', 'like', '%Receivable%')
                ->first();
                
            if (!$cashAccount) {
                throw new Exception("Accounts Receivable / Cash account not found for sale.");
            }
        }

        // To dispose:
        // Debit Acc Dep (for total accumulated depreciation)
        // Debit Cash/Receivable (sale price)
        // Credit Asset Account (for original purchase cost)
        // Difference is Gain (Credit) or Loss (Debit)
        
        $entries = [];

        // Debit Accumulated Depreciation
        if ($asset->accumulated_depreciation > 0) {
            $entries[] = [
                'chart_of_account_id' => $accDepAccount->id,
                'debit' => $asset->accumulated_depreciation,
                'credit' => 0,
            ];
        }

        // Debit Cash/Receivable
        if ($disposal->sale_price > 0) {
            $entries[] = [
                'chart_of_account_id' => $cashAccount->id,
                'debit' => $disposal->sale_price,
                'credit' => 0,
            ];
        }

        // Credit Asset
        $entries[] = [
            'chart_of_account_id' => $assetAccount->id,
            'debit' => 0,
            'credit' => $asset->purchase_cost,
        ];

        // Disposal Costs (Debit Gain/Loss Account)
        if ($disposal->disposal_costs > 0) {
            $entries[] = [
                'chart_of_account_id' => $gainLossAccount->id,
                'debit' => $disposal->disposal_costs,
                'credit' => 0,
            ];
        }

        // Calculate actual net debit and credit
        $totalDebit = collect($entries)->sum('debit');
        $totalCredit = collect($entries)->sum('credit');
        
        $difference = $totalCredit - $totalDebit;

        if ($difference > 0) {
            // Need more debit -> Loss
            $entries[] = [
                'chart_of_account_id' => $gainLossAccount->id,
                'debit' => $difference,
                'credit' => 0,
            ];
        } elseif ($difference < 0) {
            // Need more credit -> Gain
            $entries[] = [
                'chart_of_account_id' => $gainLossAccount->id,
                'debit' => 0,
                'credit' => abs($difference),
            ];
        }
        
        // Ensure no zero values are passed if they offset
        $filteredEntries = [];
        foreach ($entries as $e) {
            if ($e['debit'] > 0 || $e['credit'] > 0) {
                $filteredEntries[] = $e;
            }
        }

        $journal = $this->journalService->createAutomaticJournal([
            'company_id' => $asset->company_id,
            'date' => $disposal->date->toDateString(),
            'memo' => "{$disposal->type} of Asset: {$asset->asset_number}",
            'reference_number' => 'DISP-' . $asset->asset_number,
        ], $filteredEntries);

        $disposal->update([
            'journal_id' => $journal->id,
            'status' => 'Approved',
            'gain_loss' => $difference < 0 ? abs($difference) : -$difference // Positive is gain, negative is loss
        ]);
        
        $asset->update([
            'status' => $disposal->type, // Disposed, Sold, Written Off
            'current_book_value' => 0,
        ]);

        return $journal;
    }
}

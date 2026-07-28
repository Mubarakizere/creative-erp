<?php

namespace App\Services\Asset;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\Journal;
use App\Services\Finance\JournalService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class DepreciationService
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function calculateDepreciation(Asset $asset, Carbon $periodEnd, array $options = []): ?AssetDepreciation
    {
        if ($asset->status !== 'Active') {
            return null;
        }

        if (!$asset->in_service_date || $asset->in_service_date > $periodEnd) {
            return null;
        }
        
        if ($asset->current_book_value <= $asset->residual_value) {
            return null;
        }

        // Check if depreciation is already run for this period month/year
        $existing = AssetDepreciation::where('asset_id', $asset->id)
            ->whereYear('period_date', $periodEnd->year)
            ->whereMonth('period_date', $periodEnd->month)
            ->first();

        if ($existing) {
            return null;
        }

        $amount = 0;
        
        // Using Decimal/Database precision principles (no floats where possible, but PHP uses floats for math,
        // we'll use round($val, 2)).
        
        if ($asset->depreciation_method === 'straight_line') {
            if ($asset->useful_life <= 0) {
                throw new Exception("Useful life must be > 0 for straight line method.");
            }
            
            $depreciableBase = $asset->purchase_cost - $asset->residual_value;
            $monthlyDepreciation = $depreciableBase / $asset->useful_life;
            
            // Handle partial first month
            $serviceDate = Carbon::parse($asset->in_service_date);
            if ($serviceDate->year === $periodEnd->year && $serviceDate->month === $periodEnd->month) {
                $daysInMonth = $periodEnd->daysInMonth;
                $daysActive = $daysInMonth - $serviceDate->day + 1;
                $amount = $monthlyDepreciation * ($daysActive / $daysInMonth);
            } else {
                $amount = $monthlyDepreciation;
            }
            
        } elseif ($asset->depreciation_method === 'declining_balance') {
            // Simplified declining balance using standard rates (e.g. 20%)
            // A more complex implementation would calculate the rate from useful life.
            $rate = 1.0 / $asset->useful_life;
            $annualDepreciation = $asset->current_book_value * $rate;
            $amount = $annualDepreciation / 12;
            
        } elseif ($asset->depreciation_method === 'double_declining_balance') {
            $rate = 2.0 / $asset->useful_life;
            $annualDepreciation = $asset->current_book_value * $rate;
            $amount = $annualDepreciation / 12;
            
        } elseif ($asset->depreciation_method === 'units_of_production') {
            if (!$asset->useful_units || $asset->useful_units <= 0) {
                throw new Exception("Useful units must be > 0 for units of production method.");
            }
            if (!isset($options['units_produced']) || $options['units_produced'] < 0) {
                throw new Exception("units_produced must be provided in options and >= 0.");
            }
            
            $depreciableBase = $asset->purchase_cost - $asset->residual_value;
            $rate = $depreciableBase / $asset->useful_units;
            $amount = $rate * $options['units_produced'];
        }
        
        $amount = round($amount, 2);

        // Prevent depreciation below residual value
        if ($asset->current_book_value - $amount < $asset->residual_value) {
            $amount = $asset->current_book_value - $asset->residual_value;
        }

        if ($amount <= 0) {
            return null;
        }

        $newAccumulated = $asset->accumulated_depreciation + $amount;
        $newBookValue = $asset->current_book_value - $amount;

        return AssetDepreciation::create([
            'asset_id' => $asset->id,
            'period_date' => $periodEnd->toDateString(),
            'amount' => $amount,
            'accumulated_depreciation' => $newAccumulated,
            'book_value' => $newBookValue,
            'status' => 'Preview',
            'calculated_by' => auth()->id() ?? 1,
        ]);
    }

    public function generateMonthlyPreview(int $companyId, Carbon $periodEnd): int
    {
        $assets = Asset::where('company_id', $companyId)
            ->where('status', 'Active')
            ->where('current_book_value', '>', DB::raw('residual_value'))
            ->get();

        $count = 0;
        foreach ($assets as $asset) {
            $dep = $this->calculateDepreciation($asset, $periodEnd);
            if ($dep) $count++;
        }
        
        return $count;
    }

    public function postDepreciation(AssetDepreciation $depreciation): Journal
    {
        if ($depreciation->status !== 'Preview') {
            throw new Exception("Only preview depreciations can be posted.");
        }

        $asset = $depreciation->asset;
        $category = $asset->category;

        $expenseAccount = $category->depreciationExpenseAccount;
        $accumulatedAccount = $category->accumulatedDepreciationAccount;

        if (!$expenseAccount || !$accumulatedAccount) {
            throw new Exception("Depreciation accounts are not configured for category: {$category->name}");
        }

        $entries = [
            [
                'chart_of_account_id' => $expenseAccount->id,
                'debit' => $depreciation->amount,
                'credit' => 0,
                'branch_id' => $asset->branch_id,
                'department_id' => $asset->department_id,
                'project_id' => $asset->project_id,
            ],
            [
                'chart_of_account_id' => $accumulatedAccount->id,
                'debit' => 0,
                'credit' => $depreciation->amount,
                'branch_id' => $asset->branch_id,
                'department_id' => $asset->department_id,
                'project_id' => $asset->project_id,
            ]
        ];

        return DB::transaction(function () use ($depreciation, $asset, $entries) {
            $journal = $this->journalService->createAutomaticJournal([
                'company_id' => $asset->company_id,
                'branch_id' => $asset->branch_id,
                'department_id' => $asset->department_id,
                'project_id' => $asset->project_id,
                'date' => $depreciation->period_date->toDateString(),
                'memo' => "Depreciation for {$asset->asset_number} - Period: {$depreciation->period_date->format('Y-m')}",
                'reference_number' => 'DEP-' . $depreciation->id,
            ], $entries);

            $depreciation->update([
                'status' => 'Posted',
                'journal_id' => $journal->id,
                'approved_by' => auth()->id() ?? 1,
            ]);

            $asset->update([
                'accumulated_depreciation' => $depreciation->accumulated_depreciation,
                'current_book_value' => $depreciation->book_value,
            ]);
            
            if ($asset->current_book_value <= $asset->residual_value) {
                $asset->update(['status' => 'Fully Depreciated']);
            }

            return $journal;
        });
    }
}

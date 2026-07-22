<?php

namespace App\Services\Finance;

use App\Models\Refund;
use App\Models\Payment;
use App\Models\ApprovalWorkflow;
use App\Services\ApprovalService;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;
use App\Services\Finance\JournalService;
use App\Models\ChartOfAccount;

class RefundService
{
    use LogsActivity;
    protected ?JournalService $journalService;

    public function __construct(JournalService $journalService = null)
    {
        $this->journalService = $journalService ?? app(JournalService::class);
    }
    public function processRefund(array $data): Refund
    {
        return DB::transaction(function () use ($data) {
            $data['refund_number'] = $data['refund_number'] ?? $this->generateRefundNumber();
            
            // Check for active approval workflow
            $workflow = ApprovalWorkflow::where('module', 'Refund')
                ->where('company_id', $data['company_id'] ?? (auth()->user()->company_id ?? 1))
                ->where('is_active', true)
                ->first();
                
            if ($workflow) {
                $data['status'] = 'Pending Approval';
            } else {
                $data['status'] = 'Processed';
            }
            
            $refund = Refund::create($data);
            
            if ($workflow) {
                app(ApprovalService::class)->submit($refund, $workflow, 'Automatically submitted for approval');
            }
            
            $this->logActivity('refund_processed', $refund, [
                'refund_number' => $refund->refund_number,
                'amount' => $refund->amount,
                'status' => $refund->status
            ]);
            
            // Auto Post to Ledger
            $this->autoPostRefundToLedger($refund);
            
            return $refund;
        });
    }
    
    private function generateRefundNumber(): string
    {
        return 'REF-' . strtoupper(uniqid());
    }

    private function autoPostRefundToLedger(Refund $refund): void
    {
        // Simple logic to find system accounts (in real app this comes from settings)
        $bankAccount = ChartOfAccount::where('company_id', $refund->company_id)->where('is_system', true)->where('name', 'like', '%Bank%')->first();
        $arAccount = ChartOfAccount::where('company_id', $refund->company_id)->where('is_system', true)->where('name', 'like', '%Accounts Receivable%')->first();

        if ($bankAccount && $arAccount && $refund->amount > 0) {
            $this->journalService->createAutomaticJournal([
                'company_id' => $refund->company_id,
                'reference_number' => $refund->refund_number,
                'date' => $refund->date ?? now(),
                'memo' => 'Auto-generated journal for Refund ' . $refund->refund_number,
                'status' => 'Pending Approval'
            ], [
                [
                    'chart_of_account_id' => $arAccount->id,
                    'description' => 'Refund ' . $refund->refund_number,
                    'debit' => $refund->amount,
                    'credit' => 0,
                ],
                [
                    'chart_of_account_id' => $bankAccount->id,
                    'description' => 'Refund ' . $refund->refund_number,
                    'debit' => 0,
                    'credit' => $refund->amount,
                ]
            ]);
        }
    }
}

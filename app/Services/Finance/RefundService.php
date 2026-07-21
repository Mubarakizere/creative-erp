<?php

namespace App\Services\Finance;

use App\Models\Refund;
use App\Models\Payment;
use App\Models\ApprovalWorkflow;
use App\Services\ApprovalService;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;

class RefundService
{
    use LogsActivity;
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
            
            return $refund;
        });
    }
    
    private function generateRefundNumber(): string
    {
        return 'REF-' . strtoupper(uniqid());
    }
}

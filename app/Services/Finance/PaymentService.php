<?php

namespace App\Services\Finance;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Receipt;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\LogsActivity;

class PaymentService
{
    use LogsActivity;
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function processPayment(array $data, array $allocations = []): Payment
    {
        return DB::transaction(function () use ($data, $allocations) {
            $data['payment_number'] = $data['payment_number'] ?? $this->generatePaymentNumber();
            $payment = Payment::create($data);
            
            if (!empty($allocations)) {
                $this->allocatePayment($payment, $allocations);
            }
            
            $this->generateReceipt($payment);
            
            $this->logActivity('payment_recorded', $payment, [
                'amount' => $payment->amount,
                'payment_number' => $payment->payment_number,
                'method_id' => $payment->payment_method_id
            ]);
            
            return $payment;
        });
    }

    public function allocatePayment(Payment $payment, array $allocations): void
    {
        $totalAllocated = 0;
        
        foreach ($allocations as $alloc) {
            $invoiceId = $alloc['invoice_id'];
            $amount = $alloc['amount'] ?? ($alloc['amount_allocated'] ?? 0);
            
            $payment->allocations()->create([
                'invoice_id' => $invoiceId,
                'amount_allocated' => $amount
            ]);
            
            $invoice = Invoice::findOrFail($invoiceId);
            
            $this->invoiceService->calculateTotals($invoice);
            
            $totalAllocated += $amount;
        }
        
        if ($totalAllocated > $payment->amount) {
            throw new Exception("Allocated amount exceeds total payment amount.");
        }
    }

    public function generateReceipt(Payment $payment): Receipt
    {
        $receipt = Receipt::create([
            'company_id' => $payment->company_id,
            'payment_id' => $payment->id,
            'receipt_number' => 'RCT-' . strtoupper(uniqid()),
            'generated_at' => now(),
        ]);
        
        $this->logActivity('receipt_generated', $receipt, [
            'receipt_number' => $receipt->receipt_number,
            'payment_id' => $payment->id
        ]);
        
        return $receipt;
    }
    
    private function generatePaymentNumber(): string
    {
        return 'PAY-' . strtoupper(uniqid());
    }
}

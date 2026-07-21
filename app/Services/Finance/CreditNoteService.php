<?php

namespace App\Services\Finance;

use App\Models\CreditNote;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\LogsActivity;

class CreditNoteService
{
    use LogsActivity;
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function createCreditNote(array $data): CreditNote
    {
        return DB::transaction(function () use ($data) {
            $data['credit_note_number'] = $data['credit_note_number'] ?? $this->generateCreditNoteNumber();
            $data['remaining_balance'] = $data['amount'];
            $creditNote = CreditNote::create($data);
            
            $this->logActivity('credit_note_created', $creditNote, [
                'credit_note_number' => $creditNote->credit_note_number,
                'amount' => $creditNote->amount
            ]);
            
            return $creditNote;
        });
    }

    public function applyToInvoice(CreditNote $creditNote, Invoice $invoice, float $amount): void
    {
        if ($creditNote->status !== 'Issued') {
            throw new Exception("Credit note must be issued before it can be applied.");
        }
        
        if ($amount > $creditNote->remaining_balance) {
            throw new Exception("Amount exceeds remaining credit balance.");
        }
        
        DB::transaction(function () use ($creditNote, $invoice, $amount) {
            // In a real system, you'd create a CreditNoteApplication record.
            // For now, we adjust balances.
            $creditNote->remaining_balance -= $amount;
            
            if ($creditNote->remaining_balance <= 0) {
                $creditNote->status = 'Applied';
            }
            $creditNote->save();
            
            $invoice->paid_amount += $amount; // Treat credit as payment
            $invoice->save();
            
            $this->invoiceService->calculateTotals($invoice);
            
            $this->logActivity('credit_note_applied', $creditNote, [
                'credit_note_number' => $creditNote->credit_note_number,
                'invoice_id' => $invoice->id,
                'amount_applied' => $amount,
                'remaining_balance' => $creditNote->remaining_balance
            ]);
        });
    }
    
    private function generateCreditNoteNumber(): string
    {
        return 'CN-' . strtoupper(uniqid());
    }
}

<?php

namespace App\Services\Finance;

use App\Models\Invoice;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Traits\LogsActivity;

use App\Services\Finance\JournalService;
use App\Models\ChartOfAccount;

class InvoiceService
{
    use LogsActivity;
    protected ?JournalService $journalService;

    public function __construct(JournalService $journalService = null)
    {
        $this->journalService = $journalService ?? app(JournalService::class);
    }
    public function createInvoice(array $data, array $items = []): Invoice
    {
        return DB::transaction(function () use ($data, $items) {
            $data['invoice_number'] = $data['invoice_number'] ?? $this->generateInvoiceNumber();
            $invoice = Invoice::create($data);
            
            if (!empty($items)) {
                foreach ($items as $itemData) {
                    $invoice->items()->create($itemData);
                }
                $this->calculateTotals($invoice);
            }
            
            
            $this->logActivity('invoice_created', $invoice, ['invoice_number' => $invoice->invoice_number, 'total_amount' => $invoice->total_amount]);
            
            // Auto Post to Ledger
            $this->autoPostInvoiceToLedger($invoice);
            
            return $invoice;
        });
    }

    public function generateFromQuotation(Quotation $quotation): Invoice
    {
        return DB::transaction(function () use ($quotation) {
            $invoice = Invoice::create([
                'company_id' => $quotation->company_id,
                'client_id' => $quotation->account_id, // Assuming Quotation is linked to account/client
                'project_id' => null, // Set if needed
                'opportunity_id' => $quotation->opportunity_id,
                'quotation_id' => $quotation->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'status' => 'Draft',
                'issue_date' => now(),
                'due_date' => now()->addDays(30),
                'currency_code' => $quotation->currency ?? 'USD',
                'notes' => 'Generated from Quotation ' . $quotation->quotation_number,
            ]);

            foreach ($quotation->items as $qItem) {
                $invoice->items()->create([
                    'description' => $qItem->name ?? 'Item',
                    'quantity' => $qItem->quantity,
                    'unit_price' => $qItem->unit_price,
                    'tax_id' => $qItem->tax_id,
                    // Keep calculations simple for this logic
                ]);
            }

            $this->calculateTotals($invoice);
            
            $this->logActivity('invoice_created', $invoice, ['invoice_number' => $invoice->invoice_number, 'from_quotation' => $quotation->id]);
            
            return $invoice;
        });
    }

    public function calculateTotals(Invoice $invoice): Invoice
    {
        $subtotal = 0;
        $taxTotal = 0;
        $discountTotal = 0;
        
        foreach ($invoice->items as $item) {
            $lineTotal = $item->quantity * $item->unit_price;
            $discount = $item->discount_amount;
            $tax = $item->tax_amount;
            
            $itemNet = $lineTotal - $discount;
            $item->update([
                'total_amount' => $itemNet + $tax
            ]);
            
            $subtotal += $lineTotal;
            $discountTotal += $discount;
            $taxTotal += $tax;
        }
        
        $paidAmount = $invoice->allocations()
            ->whereHas('payment', function($q) {
                $q->whereNull('deleted_at');
            })
            ->sum('amount_allocated');

        $totalAmount = $subtotal - $discountTotal + $taxTotal;
        $balanceDue = $totalAmount - $paidAmount;
        
        $invoice->update([
            'subtotal' => $subtotal,
            'tax_total' => $taxTotal,
            'discount_total' => $discountTotal,
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'balance_due' => max(0, $balanceDue),
        ]);
        
        $this->updateStatus($invoice);
        
        return $invoice;
    }
    
    public function updateStatus(Invoice $invoice): void
    {
        if (in_array($invoice->status, ['Cancelled', 'Voided', 'Draft'])) {
            return;
        }
        
        $oldStatus = $invoice->status;
        $newStatus = $oldStatus;
        
        if ($invoice->balance_due <= 0 && $invoice->total_amount > 0) {
            $newStatus = 'Paid';
        } elseif ($invoice->paid_amount > 0) {
            $newStatus = 'Partially Paid';
        } elseif ($invoice->due_date && $invoice->due_date->isPast()) {
            $newStatus = 'Overdue';
        }
        
        if ($newStatus !== $oldStatus) {
            $invoice->update(['status' => $newStatus]);
            $this->logActivity('invoice_status_changed', $invoice, [
                'old_status' => $oldStatus,
                'new_status' => $newStatus
            ]);
        }
    }
    
    public function cancel(Invoice $invoice): void
    {
        $invoice->update(['status' => 'Cancelled']);
        $this->logActivity('invoice_status_changed', $invoice, [
            'old_status' => $invoice->status,
            'new_status' => 'Cancelled'
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        return 'INV-' . strtoupper(uniqid());
    }

    private function autoPostInvoiceToLedger(Invoice $invoice): void
    {
        // Simple logic to find system accounts (in real app this comes from settings)
        $arAccount = ChartOfAccount::where('company_id', $invoice->company_id)->where('is_system', true)->where('name', 'like', '%Accounts Receivable%')->first();
        $revenueAccount = ChartOfAccount::where('company_id', $invoice->company_id)->where('is_system', true)->where('name', 'like', '%Sales%')->first();

        if ($arAccount && $revenueAccount && $invoice->total_amount > 0) {
            $this->journalService->createAutomaticJournal([
                'company_id' => $invoice->company_id,
                'reference_number' => $invoice->invoice_number,
                'date' => $invoice->issue_date ?? now(),
                'memo' => 'Auto-generated journal for Invoice ' . $invoice->invoice_number,
                'status' => 'Pending Approval' // or 'Posted' depending on settings
            ], [
                [
                    'chart_of_account_id' => $arAccount->id,
                    'description' => 'Invoice ' . $invoice->invoice_number,
                    'debit' => $invoice->total_amount,
                    'credit' => 0,
                ],
                [
                    'chart_of_account_id' => $revenueAccount->id,
                    'description' => 'Invoice ' . $invoice->invoice_number,
                    'debit' => 0,
                    'credit' => $invoice->total_amount,
                ]
            ]);
        }
    }
}

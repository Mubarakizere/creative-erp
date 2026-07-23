<?php
namespace App\Services\Procurement;

use App\Models\SupplierPayment;
use App\Models\PurchaseInvoice;
use App\Services\Finance\JournalService;
use Illuminate\Support\Facades\DB;

class SupplierPaymentService
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function create(array $data): SupplierPayment
    {
        return DB::transaction(function() use ($data) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['created_by'] = auth()->id();
            
            $payment = SupplierPayment::create($data);
            
            if ($payment->purchase_invoice_id) {
                $invoice = PurchaseInvoice::find($payment->purchase_invoice_id);
                if ($invoice) {
                    $invoice->paid_amount += $payment->amount;
                    if ($invoice->paid_amount >= $invoice->grand_total) {
                        $invoice->status = 'paid';
                    } else {
                        $invoice->status = 'partially_paid';
                    }
                    $invoice->save();
                }
            }

            // Debit AP, Credit Cash/Bank
            if ($payment->amount > 0) {
                $apAccount = \App\Models\ChartOfAccount::where('code', '2000')->first() ?? \App\Models\ChartOfAccount::first();
                $cashAccount = \App\Models\ChartOfAccount::where('code', '1000')->first() ?? \App\Models\ChartOfAccount::first();

                if ($apAccount && $cashAccount) {
                    $this->journalService->createAutomaticJournal([
                        'company_id' => $payment->company_id,
                        'date' => $payment->payment_date,
                        'memo' => 'Supplier Payment ' . $payment->payment_number,
                    ], [
                        [
                            'chart_of_account_id' => $apAccount->id,
                            'description' => 'Accounts Payable Reduced',
                            'debit' => $payment->amount,
                            'credit' => 0,
                        ],
                        [
                            'chart_of_account_id' => $cashAccount->id,
                            'description' => 'Cash/Bank Payment',
                            'debit' => 0,
                            'credit' => $payment->amount,
                        ]
                    ]);
                }
            }

            return $payment;
        });
    }
}
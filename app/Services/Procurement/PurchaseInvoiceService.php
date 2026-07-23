<?php
namespace App\Services\Procurement;

use App\Models\PurchaseInvoice;
use App\Services\Finance\JournalService;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceService
{
    protected JournalService $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function create(array $data, array $items): PurchaseInvoice
    {
        return DB::transaction(function() use ($data, $items) {
            $data['company_id'] = $data['company_id'] ?? auth()->user()->company_id;
            $data['created_by'] = auth()->id();
            
            $invoice = PurchaseInvoice::create($data);
            $totalCost = 0;

            foreach ($items as $item) {
                $invoiceItem = $invoice->items()->create($item);
                $totalCost += $invoiceItem->total;
            }

            // Debit GRNI (or Expense), Credit Accounts Payable
            if ($totalCost > 0) {
                $grniAccount = \App\Models\ChartOfAccount::where('code', '2100')->first() ?? \App\Models\ChartOfAccount::first();
                $apAccount = \App\Models\ChartOfAccount::where('code', '2000')->first() ?? \App\Models\ChartOfAccount::first();

                if ($grniAccount && $apAccount) {
                    $this->journalService->createAutomaticJournal([
                        'company_id' => $invoice->company_id,
                        'date' => $invoice->invoice_date,
                        'memo' => 'Purchase Invoice ' . $invoice->invoice_number,
                    ], [
                        [
                            'chart_of_account_id' => $grniAccount->id,
                            'description' => 'Goods Receipt Invoiced',
                            'debit' => $totalCost,
                            'credit' => 0,
                        ],
                        [
                            'chart_of_account_id' => $apAccount->id,
                            'description' => 'Accounts Payable',
                            'debit' => 0,
                            'credit' => $totalCost,
                        ]
                    ]);
                }
            }

            return $invoice;
        });
    }
}
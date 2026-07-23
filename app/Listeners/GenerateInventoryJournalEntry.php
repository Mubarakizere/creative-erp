<?php

namespace App\Listeners;

use App\Events\InventoryUpdated;
use App\Services\Finance\AccountingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateInventoryJournalEntry
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function handle(InventoryUpdated $event): void
    {
        $type = $event->transaction->type;
        
        // Only specific transaction types affect the General Ledger natively for inventory valuation changes
        $glImpactTypes = ['adjustment', 'adjustment_gain', 'adjustment_loss', 'stock_in', 'stock_out', 'consumption', 'return'];
        
        if (in_array($type, $glImpactTypes)) {
            $this->accountingService->recordInventoryTransaction($event->inventory, $event->transaction);
        }
    }
}

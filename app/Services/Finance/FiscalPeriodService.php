<?php

namespace App\Services\Finance;

use App\Models\AccountingPeriod;
use App\Models\FiscalYear;
use App\Traits\LogsActivity;
use Illuminate\Support\Facades\DB;
use Exception;

class FiscalPeriodService
{
    use LogsActivity;

    public function createFiscalYear(array $data): FiscalYear
    {
        return DB::transaction(function () use ($data) {
            $fiscalYear = FiscalYear::create($data);
            $this->logActivity('fiscal_year_created', $fiscalYear, ['name' => $fiscalYear->name]);
            return $fiscalYear;
        });
    }

    public function createAccountingPeriod(array $data): AccountingPeriod
    {
        return DB::transaction(function () use ($data) {
            $period = AccountingPeriod::create($data);
            $this->logActivity('accounting_period_created', $period, ['name' => $period->name]);
            return $period;
        });
    }

    public function closeAccountingPeriod(AccountingPeriod $period): void
    {
        if ($period->status === 'Closed' || $period->status === 'Locked') {
            throw new Exception("Period is already closed or locked.");
        }

        $period->update(['status' => 'Closed']);
        $this->logActivity('accounting_period_closed', $period, ['name' => $period->name]);
    }

    public function closeFiscalYear(FiscalYear $fiscalYear): void
    {
        if ($fiscalYear->is_closed) {
            throw new Exception("Fiscal year is already closed.");
        }

        // Ideally, we'd check if all periods are closed, and then generate closing entries.
        // That involves moving Revenue & Expense balances into Retained Earnings.
        
        DB::transaction(function () use ($fiscalYear) {
            // Close all open periods
            $fiscalYear->periods()->update(['status' => 'Closed']);
            
            $fiscalYear->update([
                'is_closed' => true,
                'closed_at' => now(),
                'closed_by' => auth()->id(),
            ]);

            $this->logActivity('fiscal_year_closed', $fiscalYear, ['name' => $fiscalYear->name]);
        });
    }
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\FiscalYear;
use App\Models\AccountingPeriod;
use App\Models\Company;
use Carbon\Carbon;

$company = Company::first();

$year = FiscalYear::firstOrCreate(
    ['company_id' => $company->id, 'name' => 'FY 2026'],
    [
        'start_date' => '2026-01-01',
        'end_date' => '2026-12-31',
        'is_closed' => false
    ]
);

for ($i = 1; $i <= 12; $i++) {
    $start = Carbon::create(2026, $i, 1)->startOfMonth();
    $end = $start->copy()->endOfMonth();
    
    AccountingPeriod::firstOrCreate(
        ['company_id' => $company->id, 'fiscal_year_id' => $year->id, 'name' => $start->format('M Y')],
        [
            'start_date' => $start->format('Y-m-d'),
            'end_date' => $end->format('Y-m-d'),
            'status' => 'Open'
        ]
    );
}

echo "Fiscal Year and Periods seeded successfully.\n";

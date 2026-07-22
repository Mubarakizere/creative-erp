<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ReportTemplate;
use App\Services\ExportService;
use App\Services\ReportBuilderService;

class TestReportsExport extends Command
{
    protected $signature = 'test:reports-export';
    protected $description = 'Test generation and export of financial reports';

    public function handle(ExportService $exportService, ReportBuilderService $builderService)
    {
        $this->info('Starting Financial Reports Export Test...');

        // Find or create an admin user
        $user = User::first();
        if (!$user) {
            $this->error('No user found to run exports as.');
            return 1;
        }

        // Test types
        $reportTypes = [
            'profit_and_loss',
            'balance_sheet',
            'cash_flow',
            'expense_analysis',
            'budget_analysis',
            'customer_profitability',
            'project_profitability'
        ];

        $formats = ['pdf', 'xlsx', 'csv'];

        foreach ($reportTypes as $type) {
            $this->info("Testing {$type}...");
            
            // Generate a dummy template
            $template = ReportTemplate::firstOrCreate(
                ['type' => $type],
                ['name' => ucwords(str_replace('_', ' ', $type)), 'description' => 'Test Report', 'company_id' => $user->company_id]
            );

            // 1. Test Builder
            try {
                $data = $builderService->build($type, ['company_id' => $user->company_id]);
                if ($data->isEmpty()) {
                    $this->warn("  Builder returned empty collection.");
                } else {
                    $this->line("  Builder success.");
                }
            } catch (\Exception $e) {
                $this->error("  Builder failed: " . $e->getMessage());
                continue;
            }

            // 2. Test Exports
            foreach ($formats as $format) {
                try {
                    $history = $exportService->export($template, ['company_id' => $user->company_id], $format, $user->id, $user->company_id);
                    $this->line("  Export {$format} success -> " . $history->file_path);
                } catch (\Exception $e) {
                    $this->error("  Export {$format} failed: " . $e->getMessage());
                }
            }
        }

        $this->info('Done!');
        return 0;
    }
}

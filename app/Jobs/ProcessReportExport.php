<?php

namespace App\Jobs;

use App\Models\ExportHistory;
use App\Models\ReportTemplate;
use App\Services\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessReportExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes max for heavy reports
    
    protected ExportHistory $exportHistory;
    protected ReportTemplate $template;
    protected array $filters;

    public function __construct(ExportHistory $exportHistory, ReportTemplate $template, array $filters)
    {
        $this->exportHistory = $exportHistory;
        $this->template = $template;
        $this->filters = $filters;
    }

    public function handle(ExportService $exportService): void
    {
        try {
            $exportService->generateExport($this->exportHistory, $this->template, $this->filters);
        } catch (\Exception $e) {
            Log::error("Export Failed for History ID {$this->exportHistory->id}: " . $e->getMessage());
        }
    }
}

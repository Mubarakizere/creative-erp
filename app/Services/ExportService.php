<?php

namespace App\Services;

use App\Models\ExportHistory;
use App\Models\ReportTemplate;
use App\Jobs\ProcessReportExport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GenericReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Quotation;
use App\Exports\QuotationExport;

class ExportService
{
    protected ReportBuilderService $builderService;

    public function __construct(ReportBuilderService $builderService)
    {
        $this->builderService = $builderService;
    }

    /**
     * Trigger a report export. Can be processed synchronously or queued.
     */
    public function export(ReportTemplate $template, array $filters, string $format, int $userId, int $companyId = null)
    {
        $exportHistory = ExportHistory::create([
            'user_id' => $userId,
            'company_id' => $companyId,
            'report_name' => $template->name,
            'format' => $format,
            'status' => 'pending',
        ]);

        // Process immediately so the user can download the file right away.
        ProcessReportExport::dispatchSync($exportHistory, $template, $filters);

        return $exportHistory;
    }

    /**
     * Process the actual export generation. Called by the Job.
     */
    public function generateExport(ExportHistory $exportHistory, ReportTemplate $template, array $filters)
    {
        try {
            $data = $this->builderService->build($template->type, $filters);
            $fileName = 'exports/' . uniqid('report_') . '_' . time() . '.' . $exportHistory->format;
            $disk = 'local'; // Should be 's3' or 'public' based on configuration

            if (in_array($exportHistory->format, ['xlsx', 'csv'])) {
                Excel::store(new GenericReportExport($data, $template->type), $fileName, $disk);
            } elseif ($exportHistory->format === 'pdf') {
                $pdf = Pdf::loadView('admin.reports.exports.pdf', [
                    'template' => $template,
                    'data' => $data,
                    'filters' => $filters,
                ]);
                Storage::disk($disk)->put($fileName, $pdf->output());
            } else {
                throw new \Exception("Unsupported format: {$exportHistory->format}");
            }

            $exportHistory->update([
                'status' => 'completed',
                'file_path' => $fileName,
            ]);
            
        } catch (\Exception $e) {
            $exportHistory->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    public function exportQuotation(Quotation $quotation, string $format = 'pdf')
    {
        $fileName = 'exports/' . uniqid('quotation_') . '_' . time() . '.' . $format;
        $disk = 'local';
        
        if (in_array($format, ['xlsx', 'csv'])) {
            Excel::store(new QuotationExport($quotation), $fileName, $disk);
        } elseif ($format === 'pdf') {
            $pdf = Pdf::loadView('admin.crm.quotations.pdf', [
                'quotation' => $quotation,
            ]);
            Storage::disk($disk)->put($fileName, $pdf->output());
        } else {
            throw new \Exception("Unsupported format: {$format}");
        }

        return Storage::disk($disk)->path($fileName);
    }
}

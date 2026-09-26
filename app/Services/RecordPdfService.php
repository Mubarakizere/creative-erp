<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class RecordPdfService
{
    public function download(string $title, string $reference, array $details, array $columns = [], array $rows = [], array $summary = [], ?string $companyName = null)
    {
        $pdf = Pdf::loadView('admin.exports.record-pdf', compact('title', 'reference', 'details', 'columns', 'rows', 'summary', 'companyName'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['isHtml5ParserEnabled' => true, 'defaultFont' => 'Helvetica']);

        return $pdf->download(Str::slug($title . '-' . $reference) . '.pdf');
    }
}

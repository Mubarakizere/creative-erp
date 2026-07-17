<?php

namespace App\Services\Metrics;

use App\Contracts\MetricProvider;
use App\Models\Document;
use App\Models\DocumentCategory;

class DocumentMetrics implements MetricProvider
{
    public function cards(): array
    {
        return [
            'total_documents' => Document::count(),
            'document_categories' => DocumentCategory::count(),
        ];
    }

    public function widgets(): array
    {
        return [
            'latestDocuments' => Document::with('documentable')->latest()->take(5)->get(),
        ];
    }

    public function reports(): array
    {
        return [
            // Document Summary data
        ];
    }
}

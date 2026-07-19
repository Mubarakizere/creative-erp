<?php

namespace App\Services\Metrics;

use App\Services\Metrics\Traits\FiltersMetrics;

use App\Contracts\MetricProvider;
use App\Models\Document;
use App\Models\DocumentCategory;

class DocumentMetrics implements MetricProvider
{
    use FiltersMetrics;

    public function cards(array $filters = []): array
    {
        return [
            'total_documents' => $this->applyFilters(Document::query(), $filters)->count(),
            'document_categories' => DocumentCategory::count(),
        ];
    }

    public function widgets(array $filters = []): array
    {
        return [
            'latestDocuments' => $this->applyFilters(Document::query(), $filters)->with('documentable')->latest()->take(5)->get(),
        ];
    }

    public function reports(array $filters = []): array
    {
        return [
            // Document Summary data
        ];
    }
}

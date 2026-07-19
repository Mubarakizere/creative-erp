<?php

namespace App\Contracts;

interface MetricProvider
{
    /**
     * Get statistics for dashboard cards.
     *
     * @param array $filters
     * @return array
     */
    public function cards(array $filters = []): array;

    /**
     * Get data for dashboard widgets.
     *
     * @param array $filters
     * @return array
     */
    public function widgets(array $filters = []): array;

    /**
     * Get data for reports.
     *
     * @param array $filters
     * @return array
     */
    public function reports(array $filters = []): array;
}

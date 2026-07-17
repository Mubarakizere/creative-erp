<?php

namespace App\Contracts;

interface MetricProvider
{
    /**
     * Get statistics for dashboard cards.
     *
     * @return array
     */
    public function cards(): array;

    /**
     * Get data for dashboard widgets.
     *
     * @return array
     */
    public function widgets(): array;

    /**
     * Get data for reports.
     *
     * @return array
     */
    public function reports(): array;
}

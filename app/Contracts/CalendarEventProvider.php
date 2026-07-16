<?php

namespace App\Contracts;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * CalendarEventProvider Contract
 *
 * Any module that wants to display events on the ERP calendar
 * must implement this interface. This enables a pluggable architecture
 * where future modules (Leave, Attendance, Payroll, CRM, Inventory, Finance)
 * can register their own providers without modifying CalendarService.
 */
interface CalendarEventProvider
{
    /**
     * Get calendar events within the given date range.
     *
     * @param Carbon $start Start of the date range
     * @param Carbon $end End of the date range
     * @param int|null $userId Optional user filter
     * @param int|null $companyId Optional company filter
     * @return Collection Collection of CalendarEvent DTOs
     */
    public function getEvents(Carbon $start, Carbon $end, ?int $userId = null, ?int $companyId = null): Collection;

    /**
     * Get the provider type identifier.
     *
     * @return string
     */
    public function getType(): string;

    /**
     * Get the default color for this event type.
     *
     * @return string Hex color code
     */
    public function getColor(): string;
}

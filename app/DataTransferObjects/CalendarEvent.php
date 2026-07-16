<?php

namespace App\DataTransferObjects;

use Illuminate\Support\Carbon;

/**
 * Standardized calendar event DTO.
 *
 * All calendar providers return collections of this DTO,
 * providing a unified format for the calendar UI regardless
 * of the event source (Meeting, Task, Milestone, Project, etc.).
 */
class CalendarEvent
{
    public function __construct(
        public readonly string $id,
        public readonly string $title,
        public readonly Carbon $start,
        public readonly ?Carbon $end,
        public readonly string $type,       // meeting, task, milestone, project
        public readonly string $color,      // Hex color for calendar display
        public readonly ?string $url,       // Link to the event detail page
        public readonly bool $allDay,       // Whether this is an all-day event
        public readonly array $meta = [],   // Additional metadata (status, attendees, etc.)
    ) {}

    /**
     * Convert to array for JSON serialization (used by calendar AJAX endpoint).
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start->toIso8601String(),
            'end' => $this->end?->toIso8601String(),
            'type' => $this->type,
            'color' => $this->color,
            'url' => $this->url,
            'allDay' => $this->allDay,
            'meta' => $this->meta,
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class GenericReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $data;
    protected string $type;

    public function __construct(Collection $data, string $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return match($this->type) {
            'project_summary' => ['ID', 'Name', 'Client', 'Status', 'Progress', 'Manager'],
            'task_summary' => ['ID', 'Task Name', 'Project', 'Status', 'Priority', 'Due Date'],
            'time_summary' => ['ID', 'User', 'Project', 'Task', 'Duration (mins)', 'Date'],
            'user_productivity' => ['User', 'Role', 'Tasks Count', 'Total Hours'],
            default => ['ID', 'Data'],
        };
    }

    public function map($row): array
    {
        return match($this->type) {
            'project_summary' => [
                $row->id,
                $row->name,
                $row->client?->name ?? 'N/A',
                $row->status,
                $row->progress . '%',
                $row->manager?->name ?? 'N/A',
            ],
            'task_summary' => [
                $row->id,
                $row->name,
                $row->project?->name ?? 'N/A',
                $row->status,
                $row->priority,
                $row->due_date?->format('Y-m-d') ?? 'N/A',
            ],
            'time_summary' => [
                $row->id,
                $row->user?->name ?? 'N/A',
                $row->project?->name ?? 'N/A',
                $row->task?->name ?? 'N/A',
                $row->duration_minutes,
                $row->start_time?->format('Y-m-d') ?? 'N/A',
            ],
            'user_productivity' => [
                $row->name,
                $row->roles?->first()?->name ?? 'N/A',
                $row->tasks_count,
                round(($row->time_entries_sum_duration_minutes ?? 0) / 60, 2),
            ],
            default => [
                $row->id,
                json_encode($row),
            ],
        };
    }
}

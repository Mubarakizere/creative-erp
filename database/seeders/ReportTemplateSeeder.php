<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportTemplate;
use App\Models\Company;

class ReportTemplateSeeder extends Seeder
{
    public function run()
    {
        $companyId = Company::first()->id ?? 1;

        $templates = [
            [
                'name' => 'Global Executive Summary',
                'description' => 'A high-level view of system-wide performance and metrics across all modules.',
                'type' => 'executive',
                'is_system' => true,
                'company_id' => $companyId,
                'created_by' => 1,
                'filters' => [],
                'layout' => [
                    'chartType' => 'table',
                    'columns' => []
                ],
            ],
            [
                'name' => 'Project Health Report',
                'description' => 'Detailed summary of all projects, statuses, and managers.',
                'type' => 'project_summary',
                'is_system' => true,
                'company_id' => $companyId,
                'created_by' => 1,
                'filters' => [],
                'layout' => [
                    'chartType' => 'doughnut',
                    'columns' => ['id', 'name', 'client.name', 'status', 'progress', 'created_at']
                ],
            ],
            [
                'name' => 'Employee Productivity Matrix',
                'description' => 'Detailed analysis of user tasks and time entries.',
                'type' => 'user_productivity',
                'is_system' => true,
                'company_id' => $companyId,
                'created_by' => 1,
                'filters' => [],
                'layout' => [
                    'chartType' => 'bar',
                    'columns' => ['id', 'name', 'assigned_tasks_count', 'time_entries_sum_duration_minutes']
                ],
            ]
        ];

        foreach ($templates as $data) {
            ReportTemplate::firstOrCreate([
                'name' => $data['name'],
                'type' => $data['type']
            ], $data);
        }
    }
}

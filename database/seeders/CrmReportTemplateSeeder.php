<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ReportTemplate;
use App\Models\Company;

class CrmReportTemplateSeeder extends Seeder
{
    public function run()
    {
        $companyId = Company::first()->id ?? 1;

        $templates = [
            [
                'name' => 'CRM Pipeline Report',
                'description' => 'Detailed view of all opportunities across the pipeline.',
                'type' => 'crm_pipeline',
                'is_system' => true,
                'company_id' => $companyId,
                'created_by' => 1,
                'filters' => [],
                'layout' => [
                    'chartType' => 'doughnut',
                    'columns' => ['id', 'name', 'expected_revenue', 'probability', 'status', 'pipeline.name', 'stage.name', 'expected_close_date']
                ],
            ],
            [
                'name' => 'CRM Lead Report',
                'description' => 'Analysis of all leads by status and source.',
                'type' => 'crm_leads',
                'is_system' => true,
                'company_id' => $companyId,
                'created_by' => 1,
                'filters' => [],
                'layout' => [
                    'chartType' => 'bar',
                    'columns' => ['id', 'first_name', 'last_name', 'email', 'status', 'source', 'expected_value']
                ],
            ],
            [
                'name' => 'Lead Conversion Report',
                'description' => 'Track leads that successfully converted to opportunities.',
                'type' => 'crm_conversions',
                'is_system' => true,
                'company_id' => $companyId,
                'created_by' => 1,
                'filters' => [],
                'layout' => [
                    'chartType' => 'table',
                    'columns' => ['id', 'first_name', 'last_name', 'converted_at', 'convertedOpportunity.name', 'convertedOpportunity.expected_revenue']
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

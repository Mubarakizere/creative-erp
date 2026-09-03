<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds for Creative Construction Management System.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Drawings & Blueprints',
                'description' => 'Architectural, structural, MEP, and site engineering drawings.',
                'color' => '#2563eb',
                'sort_order' => 1,
            ],
            [
                'name' => 'Bill of Quantities (BOQ)',
                'description' => 'BOQ breakdown documents, cost estimates, and material schedules.',
                'color' => '#059669',
                'sort_order' => 2,
            ],
            [
                'name' => 'Contracts & Legal',
                'description' => 'Client agreements, contractor contracts, sub-contracts, and MOUs.',
                'color' => '#7c3aed',
                'sort_order' => 3,
            ],
            [
                'name' => 'Planning & Schedules',
                'description' => 'Project timelines, Gantt charts, phasing milestones, and look-ahead plans.',
                'color' => '#d97706',
                'sort_order' => 4,
            ],
            [
                'name' => 'Permits & Approvals',
                'description' => 'Municipal permits, environmental approvals, and building authority clearances.',
                'color' => '#dc2626',
                'sort_order' => 5,
            ],
            [
                'name' => 'Insurances & Guarantees',
                'description' => 'Contractor All Risk (CAR) policies, performance bonds, and advance payment guarantees.',
                'color' => '#0891b2',
                'sort_order' => 6,
            ],
            [
                'name' => 'Engineers Certifications',
                'description' => 'Structural integrity certificates, site safety sign-offs, and quality assurance inspection reports.',
                'color' => '#4f46e5',
                'sort_order' => 7,
            ],
        ];

        foreach ($categories as $cat) {
            DocumentCategory::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'uuid' => (string) Str::uuid(),
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'color' => $cat['color'],
                    'sort_order' => $cat['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}

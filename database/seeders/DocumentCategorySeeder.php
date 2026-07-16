<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Contracts',
            'Invoices',
            'Receipts',
            'Reports',
            'Presentations',
            'Policies',
            'Guidelines',
            'Technical Specs',
            'Other',
        ];

        foreach ($categories as $categoryName) {
            \App\Models\DocumentCategory::firstOrCreate(
                ['name' => $categoryName],
                [
                    'description' => "Category for $categoryName",
                    'is_active' => true,
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = \App\Models\DocumentCategory::all();
        
        if ($categories->isEmpty()) {
            $this->call(DocumentCategorySeeder::class);
            $categories = \App\Models\DocumentCategory::all();
        }

        $users = \App\Models\User::all();
        if ($users->isEmpty()) {
            return; // Cannot seed documents without users
        }

        // Seed documents for Projects
        $projects = \App\Models\Project::all();
        foreach ($projects as $project) {
            // Create 1-3 documents for each project
            $count = rand(1, 3);
            for ($i = 0; $i < $count; $i++) {
                \App\Models\Document::factory()->create([
                    'documentable_type' => get_class($project),
                    'documentable_id' => $project->id,
                    'document_category_id' => $categories->random()->id,
                    'uploaded_by' => $users->random()->id,
                ]);
            }
        }
        
        // Seed documents for Clients
        $clients = \App\Models\Client::all();
        foreach ($clients as $client) {
            if (rand(0, 1)) { // 50% chance to have a document
                \App\Models\Document::factory()->create([
                    'documentable_type' => get_class($client),
                    'documentable_id' => $client->id,
                    'document_category_id' => $categories->random()->id,
                    'uploaded_by' => $users->random()->id,
                ]);
            }
        }
        
        // Seed documents for Tasks
        $tasks = \App\Models\Task::all();
        foreach ($tasks as $task) {
            if (rand(0, 1)) { // 50% chance to have a document
                \App\Models\Document::factory()->create([
                    'documentable_type' => get_class($task),
                    'documentable_id' => $task->id,
                    'document_category_id' => $categories->random()->id,
                    'uploaded_by' => $users->random()->id,
                ]);
            }
        }
    }
}

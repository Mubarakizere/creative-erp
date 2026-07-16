<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extensions = ['pdf', 'docx', 'xlsx', 'png', 'jpg'];
        $extension = $this->faker->randomElement($extensions);
        $originalName = $this->faker->words(3, true) . '.' . $extension;
        
        return [
            'document_category_id' => \App\Models\DocumentCategory::factory(),
            'original_name' => $originalName,
            'file_path' => 'documents/' . Str::uuid() . '.' . $extension,
            'extension' => $extension,
            'mime_type' => 'application/octet-stream',
            'size' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
            'version' => '1.0',
            'uploaded_by' => \App\Models\User::factory(),
            'notes' => $this->faker->optional()->sentence(),
            
            // These will be overridden when creating the document, 
            // but we provide defaults for independent factory usage
            'documentable_type' => 'App\Models\Project',
            'documentable_id' => \App\Models\Project::factory(),
        ];
    }
}

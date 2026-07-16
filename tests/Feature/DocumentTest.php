<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_admin_can_upload_document()
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        
        $project = Project::factory()->create();

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($admin)->post(route('admin.documents.store'), [
            'documentable_type' => Project::class,
            'documentable_id' => $project->id,
            'file' => $file,
        ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('documents', [
            'original_name' => 'document.pdf',
            'documentable_type' => Project::class,
            'documentable_id' => $project->id,
        ]);
        
        $document = Document::first();
        Storage::disk('public')->assertExists($document->file_path);
    }
}

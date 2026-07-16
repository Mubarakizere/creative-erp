<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\DocumentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class DocumentCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions for testing
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
    }

    public function test_admin_can_view_document_categories()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');
        
        DocumentCategory::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.document-categories.index'));

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    public function test_admin_can_create_document_category()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $response = $this->actingAs($admin)->post(route('admin.document-categories.store'), [
            'name' => 'New Category',
            'description' => 'Test description',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.document-categories.index'));
        $this->assertDatabaseHas('document_categories', [
            'name' => 'New Category',
        ]);
    }
}

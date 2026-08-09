<?php

namespace Tests\Feature;

use App\Models\DocumentationArticle;
use App\Models\DocumentationCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class DocumentationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create required permissions
        Permission::firstOrCreate(['name' => 'documentation.view']);
        Permission::firstOrCreate(['name' => 'documentation.create']);
        Permission::firstOrCreate(['name' => 'documentation.update']);
        Permission::firstOrCreate(['name' => 'documentation.delete']);

        // Set up roles
        $this->adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $this->adminRole->givePermissionTo(Permission::all());

        $this->userRole = Role::firstOrCreate(['name' => 'Employee']);
        $this->userRole->givePermissionTo(['documentation.view']);
    }

    public function test_can_view_help_center()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $category = DocumentationCategory::create([
            'name' => 'Getting Started',
            'slug' => 'getting-started',
            'order' => 1,
            'is_active' => true
        ]);

        DocumentationArticle::create([
            'documentation_category_id' => $category->id,
            'title' => 'Test Article',
            'slug' => 'test-article',
            'content' => 'Test Content',
            'order' => 1,
            'status' => 'published'
        ]);

        $response = $this->actingAs($user)->get(route('admin.documentation.index'));
        $response->assertStatus(200);
        $response->assertSee('Getting Started');
        $response->assertSee('Test Article');
    }

    public function test_can_view_article()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $category = DocumentationCategory::create([
            'name' => 'Getting Started',
            'slug' => 'getting-started',
            'order' => 1,
            'is_active' => true
        ]);

        $article = DocumentationArticle::create([
            'documentation_category_id' => $category->id,
            'title' => 'Test Article',
            'slug' => 'test-article',
            'content' => '# Markdown Header',
            'order' => 1,
            'status' => 'published'
        ]);

        $response = $this->actingAs($user)->get(route('admin.documentation.show', ['getting-started', 'test-article']));
        $response->assertStatus(200);
        $response->assertSee('Test Article');
        $response->assertSee('Markdown Header');
    }

    public function test_can_search_articles()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee');

        $category = DocumentationCategory::create([
            'name' => 'Getting Started',
            'slug' => 'getting-started',
            'order' => 1,
            'is_active' => true
        ]);

        DocumentationArticle::create([
            'documentation_category_id' => $category->id,
            'title' => 'How to do X',
            'slug' => 'how-to-do-x',
            'content' => 'Detailed steps to perform X',
            'order' => 1,
            'status' => 'published'
        ]);

        $response = $this->actingAs($user)->get(route('admin.documentation.search', ['q' => 'perform X']));
        $response->assertStatus(200);
        $response->assertSee('How to do X');
    }

    public function test_unauthorized_user_cannot_manage_categories()
    {
        $user = User::factory()->create();
        $user->assignRole('Employee'); // only view permission

        $response = $this->actingAs($user)->get(route('admin.documentation-categories.index'));
        $response->assertStatus(403);
    }

    public function test_authorized_user_can_manage_categories()
    {
        $user = User::factory()->create();
        $user->assignRole('Administrator');

        $response = $this->actingAs($user)->get(route('admin.documentation-categories.index'));
        $response->assertStatus(200);

        $postResponse = $this->actingAs($user)->post(route('admin.documentation-categories.store'), [
            'name' => 'New Category',
            'slug' => 'new-category',
            'order' => 5,
            'is_active' => 1
        ]);
        
        $postResponse->assertRedirect(route('admin.documentation-categories.index'));
        $this->assertDatabaseHas('documentation_categories', ['name' => 'New Category']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    protected $adminUser;
    protected $regularUser;
    protected $project;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Permissions
        $permissions = [
            'comment.view', 'comment.create', 'comment.update', 'comment.delete',
            'comment.pin', 'comment.internal', 'project.view'
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin User
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole->syncPermissions(Permission::all());

        $this->adminUser = User::factory()->create();
        $this->adminUser->assignRole($adminRole);

        // Create Regular User
        $regularRole = Role::firstOrCreate(['name' => 'Employee']);
        $regularRole->givePermissionTo(['comment.view', 'comment.create', 'project.view']);
        
        $this->regularUser = User::factory()->create();
        $this->regularUser->assignRole($regularRole);

        // Create a Project (Commentable model)
        $this->project = Project::factory()->create();
    }

    public function test_user_can_create_comment()
    {
        $this->actingAs($this->regularUser);

        $payload = [
            'body' => 'This is a test comment',
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
        ];

        $response = $this->post(route('admin.comments.store'), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('comments', [
            'body' => 'This is a test comment',
            'user_id' => $this->regularUser->id,
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
            'is_internal' => 0,
        ]);
    }

    public function test_user_can_reply_to_comment()
    {
        $this->actingAs($this->regularUser);

        $parentComment = Comment::factory()->create([
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
        ]);

        $payload = [
            'body' => 'This is a reply',
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
            'parent_id' => $parentComment->id,
        ];

        $response = $this->post(route('admin.comments.store'), $payload);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('comments', [
            'body' => 'This is a reply',
            'parent_id' => $parentComment->id,
        ]);
    }

    public function test_admin_can_create_internal_note()
    {
        $this->actingAs($this->adminUser);

        $payload = [
            'body' => 'This is an internal note',
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
            'is_internal' => 1,
        ];

        $response = $this->post(route('admin.comments.store'), $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'body' => 'This is an internal note',
            'is_internal' => 1,
        ]);
    }

    public function test_regular_user_cannot_create_internal_note()
    {
        $this->actingAs($this->regularUser);

        $payload = [
            'body' => 'This is an internal note',
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
            'is_internal' => 1,
        ];

        $response = $this->post(route('admin.comments.store'), $payload);

        // Validation rule will unset is_internal if no permission, or fail validation. 
        // Our controller code does $validated['is_internal'] = request()->boolean('is_internal')
        // Wait, the FormRequest uses prepareForValidation to set it based on permission.
        
        $this->assertDatabaseHas('comments', [
            'body' => 'This is an internal note',
            'is_internal' => 0, // Should be 0 since the user doesn't have permission
        ]);
    }

    public function test_admin_can_pin_comment()
    {
        $this->actingAs($this->adminUser);

        $comment = Comment::factory()->create([
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
        ]);

        $response = $this->post(route('admin.comments.pin', $comment));

        $response->assertRedirect();
        
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'is_pinned' => true,
        ]);
    }

    public function test_regular_user_cannot_pin_comment()
    {
        $this->actingAs($this->regularUser);

        $comment = Comment::factory()->create([
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
        ]);

        $response = $this->post(route('admin.comments.pin', $comment));

        $response->assertForbidden();
    }

    public function test_user_can_delete_own_comment()
    {
        $this->actingAs($this->regularUser);

        $comment = Comment::factory()->create([
            'user_id' => $this->regularUser->id,
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
        ]);

        $response = $this->delete(route('admin.comments.destroy', $comment));

        $response->assertRedirect();
        
        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_cannot_delete_others_comment()
    {
        $this->actingAs($this->regularUser);

        $otherUser = User::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'commentable_type' => Project::class,
            'commentable_id' => $this->project->id,
        ]);

        $response = $this->delete(route('admin.comments.destroy', $comment));

        $response->assertForbidden();
        
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'deleted_at' => null,
        ]);
    }
}

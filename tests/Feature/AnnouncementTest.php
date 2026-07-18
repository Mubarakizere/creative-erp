<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super-admin']);
        
        $permissions = ['create_announcements', 'view_announcements', 'edit_announcements', 'delete_announcements', 'publish_announcements'];
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }
        $role->syncPermissions($permissions);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super-admin');
        
        $this->user = User::factory()->create();
    }

    public function test_admin_can_create_announcement()
    {
        $this->actingAs($this->admin);

        $data = [
            'title' => 'System Update',
            'content' => 'The system will be updated tonight.',
            'category' => 'info',
            'priority' => 'high',
            'audience_type' => 'entire_system',
            'is_published' => true,
        ];

        $response = $this->post(route('admin.announcements.store'), $data);

        $response->assertRedirect(route('admin.announcements.index'));
        $this->assertDatabaseHas('announcements', [
            'title' => 'System Update',
            'is_published' => 1,
        ]);
    }

    public function test_regular_user_cannot_create_announcement()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($this->user);

        $data = [
            'title' => 'System Update',
            'content' => 'The system will be updated tonight.',
            'category' => 'info',
            'priority' => 'high',
            'audience_type' => 'entire_system',
            'is_published' => true,
        ];

        $response = $this->post(route('admin.announcements.store'), $data);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('announcements', [
            'title' => 'System Update',
        ]);
    }

    public function test_user_can_view_published_announcement()
    {
        $this->withoutExceptionHandling();
        $announcement = Announcement::create([
            'title' => 'System Update',
            'content' => 'The system will be updated tonight.',
            'category' => 'info',
            'priority' => 'high',
            'audience_type' => 'entire_system',
            'is_published' => true,
            'published_at' => now(),
            'creator_id' => $this->admin->id,
        ]);

        $this->actingAs($this->user);

        $response = $this->get(route('admin.announcements.show', $announcement));

        $response->assertOk();
        $response->assertSee('System Update');
    }
}

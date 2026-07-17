<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Notification;
use App\Models\NotificationPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $employee;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure roles/permissions exist
        $this->artisan('db:seed', ['--class' => 'RolesAndPermissionsSeeder']);
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');
        
        $this->employee = User::factory()->create();
        $this->employee->assignRole('Employee');
    }

    public function test_user_can_view_notifications()
    {
        $notification = Notification::factory()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => get_class($this->admin),
            'notifiable_id' => $this->admin->id,
            'type' => \App\Notifications\AppNotification::class,
            'data' => [
                'title' => 'Test Notification',
                'message' => 'This is a test notification.',
                'category' => 'system'
            ],
            'read_at' => null,
            'category' => 'system',
            'priority' => 'Normal'
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.notifications.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Notification');
    }

    public function test_user_can_mark_notification_as_read()
    {
        $notification = Notification::factory()->create([
            'id' => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => get_class($this->admin),
            'notifiable_id' => $this->admin->id,
            'type' => \App\Notifications\AppNotification::class,
            'data' => [],
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.notifications.mark-read', $notification->id));

        $response->assertRedirect();
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_as_read()
    {
        Notification::factory()->count(3)->create([
            'id' => fn() => \Illuminate\Support\Str::uuid()->toString(),
            'notifiable_type' => get_class($this->admin),
            'notifiable_id' => $this->admin->id,
            'type' => \App\Notifications\AppNotification::class,
            'data' => [],
            'read_at' => null,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.notifications.mark-all-read'));

        $response->assertRedirect();
        $this->assertEquals(0, $this->admin->unreadNotifications()->count());
    }

    public function test_user_can_update_preferences()
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.notifications.preferences.update'), [
                'email' => true,
                'database' => false,
                'system' => true,
                'meetings' => false
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $preference = NotificationPreference::where('user_id', $this->admin->id)->first();
        
        $this->assertTrue($preference->email);
        $this->assertFalse($preference->database);
        $this->assertTrue($preference->system);
        $this->assertFalse($preference->meetings);
    }
}

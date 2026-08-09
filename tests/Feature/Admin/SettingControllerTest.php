<?php

namespace Tests\Feature\Admin;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed all permissions so the sidebar renders without errors
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Policies reference permissions not yet in the seeder — create them
        // so sidebar rendering doesn't crash when checking viewAny on models.
        $extraPermissions = [
            'notification.announcement', 'notification.delete', 'notification.manage', 'notification.view',
            'crm.pipeline', 'crm.activities', 'crm.convert', 'crm.manage',
            'comment.internal', 'comment.pin',
            'document.download', 'document.replace',
            'meeting.cancel', 'meeting.invite',
            'project.close', 'project.reopen',
            'journal.post',
            'procurement.approve',
            'report.export',
            'period.manage',
            'user.reset-password',
            'warehouse.bin', 'warehouse.count', 'warehouse.manage', 'warehouse.return', 'warehouse.ship',
        ];
        foreach ($extraPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function test_guest_cannot_access_settings()
    {
        $this->get(route('admin.settings.index'))
             ->assertRedirect(route('login'));
    }

    public function test_user_without_permission_cannot_access_settings()
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::firstOrCreate(['name' => 'Admin']);
        $user->assignRole($role);
        $user->load('roles');
        
        $this->actingAs($user)
             ->get(route('admin.settings.index'))
             ->assertForbidden();
    }

    public function test_user_with_view_permission_can_access_settings()
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::firstOrCreate(['name' => 'Admin']);
        $user->assignRole($role);
        $user->load('roles');
        $user->givePermissionTo('settings.view');

        $this->actingAs($user)
             ->get(route('admin.settings.index'))
             ->assertSuccessful();
    }

    public function test_user_without_manage_permission_cannot_update_settings()
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::firstOrCreate(['name' => 'Admin']);
        $user->assignRole($role);
        $user->load('roles');
        $user->givePermissionTo('settings.view');

        $this->actingAs($user)
             ->post(route('admin.settings.update'), [
                 'settings' => [
                     'system_name' => 'Test',
                 ]
             ])
             ->assertForbidden();
    }

    public function test_user_with_manage_permission_can_update_settings()
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::firstOrCreate(['name' => 'Admin']);
        $user->assignRole($role);
        $user->load('roles');
        $user->givePermissionTo('settings.manage');

        $this->actingAs($user)
             ->post(route('admin.settings.update'), [
                 'settings' => [
                     'system_name' => 'Updated System Name',
                 ]
             ])
             ->assertRedirect(route('admin.settings.index'))
             ->assertSessionHas('success');

        $this->assertDatabaseHas('settings', [
            'key' => 'system_name',
            'value' => 'Updated System Name',
        ]);
    }

    public function test_invalid_type_is_rejected()
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::firstOrCreate(['name' => 'Admin']);
        $user->assignRole($role);
        $user->load('roles');
        $user->givePermissionTo('settings.manage');

        $this->actingAs($user)
             ->post(route('admin.settings.update'), [
                 'settings' => [
                     'maintenance_mode' => ['invalid_array'],
                 ]
             ])
             ->assertSessionHasErrors('settings.maintenance_mode');
    }

    public function test_unknown_setting_key_is_ignored()
    {
        $user = User::factory()->create(['status' => 'active']);
        $role = Role::firstOrCreate(['name' => 'Admin']);
        $user->assignRole($role);
        $user->load('roles');
        $user->givePermissionTo('settings.manage');

        $this->actingAs($user)
             ->post(route('admin.settings.update'), [
                 'settings' => [
                     'system_name' => 'Valid',
                     'unknown_key' => 'Malicious Value',
                 ]
             ]);
             
        $this->assertDatabaseMissing('settings', [
            'key' => 'unknown_key',
        ]);
        
        $this->assertDatabaseHas('settings', [
            'key' => 'system_name',
            'value' => 'Valid'
        ]);
    }
}

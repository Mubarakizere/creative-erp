<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingsIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_system_name_helper_returns_configured_name_or_fallback()
    {
        $this->assertEquals(config('app.name'), system_name());
        
        Setting::updateOrCreate(['key' => 'system_name'], ['value' => 'Test ERP System', 'group' => 'general']);
        
        // Cache needs to be cleared as settings service caches values
        app(\App\Services\SettingsService::class)->clearCache();
        
        $this->assertEquals('Test ERP System', system_name());
    }

    public function test_format_date_helper_uses_configured_format()
    {
        $date = Carbon::parse('2026-08-09');
        $this->assertEquals('Aug 9, 2026', format_date($date)); // Fallback M j, Y

        Setting::updateOrCreate(['key' => 'date_format'], ['value' => 'd/m/Y', 'group' => 'general']);
        app(\App\Services\SettingsService::class)->clearCache();

        $this->assertEquals('09/08/2026', format_date($date));
        $this->assertNull(format_date(null));
    }

    public function test_format_time_helper_uses_configured_format()
    {
        $time = Carbon::parse('2026-08-09 14:30:00');
        $this->assertEquals('2:30 PM', format_time($time)); // Fallback g:i A

        Setting::updateOrCreate(['key' => 'time_format'], ['value' => 'H:i:s', 'group' => 'general']);
        app(\App\Services\SettingsService::class)->clearCache();

        $this->assertEquals('14:30:00', format_time($time));
    }

    public function test_maintenance_mode_allows_requests_when_false()
    {
        Setting::updateOrCreate(['key' => 'maintenance_mode'], ['value' => '0', 'type' => 'boolean', 'group' => 'general']);
        app(\App\Services\SettingsService::class)->clearCache();

        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_maintenance_mode_blocks_normal_users_and_guests()
    {
        Setting::updateOrCreate(['key' => 'maintenance_mode'], ['value' => '1', 'type' => 'boolean', 'group' => 'general']);
        app(\App\Services\SettingsService::class)->clearCache();

        // Guest to non-login page
        $response = $this->get('/');
        $response->assertStatus(503);

        // Normal User
        $user = User::factory()->create(['status' => 'active']);
        $this->actingAs($user);
        
        $response = $this->get('/');
        $response->assertStatus(503);
    }

    public function test_maintenance_mode_allows_login_routes()
    {
        Setting::updateOrCreate(['key' => 'maintenance_mode'], ['value' => '1', 'type' => 'boolean', 'group' => 'general']);
        app(\App\Services\SettingsService::class)->clearCache();

        $response = $this->get(route('login'));
        $response->assertStatus(200);
    }

    public function test_super_admin_can_bypass_maintenance_mode()
    {
        Setting::updateOrCreate(['key' => 'maintenance_mode'], ['value' => '1', 'type' => 'boolean', 'group' => 'general']);
        app(\App\Services\SettingsService::class)->clearCache();

        $admin = User::factory()->create(['status' => 'active']);
        $role = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin->assignRole($role);
        
        $this->actingAs($admin);
        
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}

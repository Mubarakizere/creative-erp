<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\SettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SettingsService $settingsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingsService = new SettingsService();
        Cache::flush();
    }

    public function test_can_set_and_get_text_setting()
    {
        $this->settingsService->set('app_name', 'Creative ERP');

        $this->assertEquals('Creative ERP', $this->settingsService->get('app_name'));
        
        $this->assertDatabaseHas('settings', [
            'key' => 'app_name',
            'value' => 'Creative ERP',
            'type' => 'text',
        ]);
    }

    public function test_can_set_and_get_boolean_setting()
    {
        $this->settingsService->set('maintenance_mode', true, 'general', 'boolean');
        $this->settingsService->set('debug_mode', false, 'general', 'boolean');

        $this->assertTrue($this->settingsService->get('maintenance_mode'));
        $this->assertFalse($this->settingsService->get('debug_mode'));
    }

    public function test_can_set_and_get_integer_setting()
    {
        $this->settingsService->set('session_timeout', 120, 'security', 'integer');

        $this->assertSame(120, $this->settingsService->get('session_timeout'));
    }

    public function test_can_set_and_get_json_setting()
    {
        $data = ['width' => 1920, 'height' => 1080];
        $this->settingsService->set('image_dimensions', $data, 'general', 'json');

        $retrieved = $this->settingsService->get('image_dimensions');

        $this->assertIsArray($retrieved);
        $this->assertEquals($data, $retrieved);
    }

    public function test_get_returns_default_if_not_found()
    {
        $this->assertEquals('default_value', $this->settingsService->get('non_existent', 'default_value'));
    }

    public function test_set_many_creates_multiple_settings()
    {
        $this->settingsService->setMany([
            'site_title' => 'My Site',
            'is_active' => true,
        ], 'general');

        $this->assertEquals('My Site', $this->settingsService->get('site_title'));
        $this->assertTrue($this->settingsService->get('is_active'));
    }

    public function test_grouped_returns_settings_grouped_by_category()
    {
        $this->settingsService->set('site_name', 'ERP', 'general');
        $this->settingsService->set('smtp_host', 'localhost', 'email');

        $grouped = $this->settingsService->grouped();

        $this->assertArrayHasKey('general', $grouped);
        $this->assertArrayHasKey('email', $grouped);
        $this->assertEquals('ERP', $grouped['general']['site_name']);
        $this->assertEquals('localhost', $grouped['email']['smtp_host']);
    }

    public function test_settings_are_cached()
    {
        $this->settingsService->set('test_cache', 'value1');

        $this->assertEquals('value1', $this->settingsService->get('test_cache'));

        // Manually update the database bypassing the service
        Setting::where('key', 'test_cache')->update(['value' => 'value2']);

        // Since it's cached, the service should still return 'value1'
        $this->assertEquals('value1', $this->settingsService->get('test_cache'));

        // Clear cache and try again
        $this->settingsService->clearCache();
        $this->assertEquals('value2', $this->settingsService->get('test_cache'));
    }
}

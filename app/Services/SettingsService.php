<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    /**
     * Cache key for settings.
     */
    protected const CACHE_KEY = 'global_settings';

    /**
     * Get all settings as a key-value array.
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return Setting::pluck('value', 'key')->toArray();
        });
    }

    /**
     * Get settings grouped by their group name.
     */
    public function grouped(): array
    {
        return Cache::rememberForever(self::CACHE_KEY . '_grouped', function () {
            $settings = Setting::all();
            $grouped = [];

            foreach ($settings as $setting) {
                $grouped[$setting->group][$setting->key] = $this->castValue($setting->value, $setting->type);
            }

            return $grouped;
        });
    }

    /**
     * Get a specific setting value.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        if (array_key_exists($key, $settings)) {
            $settingModel = Setting::where('key', $key)->first();
            if ($settingModel) {
                return $this->castValue($settings[$key], $settingModel->type);
            }
        }

        return $default;
    }

    /**
     * Set a specific setting.
     */
    public function set(string $key, mixed $value, string $group = 'general', string $type = 'text'): Setting
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : $value,
                'group' => $group,
                'type' => $type,
            ]
        );

        $this->clearCache();

        return $setting;
    }

    /**
     * Set multiple settings at once.
     */
    public function setMany(array $settings, string $group = 'general'): void
    {
        foreach ($settings as $key => $value) {
            $type = is_bool($value) ? 'boolean' : (is_array($value) ? 'json' : 'text');
            $this->set($key, $value, $group, $type);
        }
    }

    /**
     * Clear settings cache.
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY . '_grouped');
    }

    /**
     * Cast value based on type.
     */
    protected function castValue(?string $value, string $type): mixed
    {
        if (is_null($value)) {
            return null;
        }

        return match ($type) {
            'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }
}

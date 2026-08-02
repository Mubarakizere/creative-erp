<?php

use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('website_setting')) {
    /**
     * Get a website setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function website_setting($key, $default = null)
    {
        $settings = Cache::rememberForever('website_settings', function () {
            return WebsiteSetting::pluck('value', 'key')->toArray();
        });

        return $settings[$key] ?? $default;
    }
}

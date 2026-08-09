<?php

use App\Services\SettingsService;
use Carbon\Carbon;

if (!function_exists('system_name')) {
    /**
     * Get the configured system name, falling back to config('app.name').
     *
     * @param string|null $default
     * @return string
     */
    function system_name(?string $default = null): string
    {
        return app(SettingsService::class)->get('system_name', $default ?? config('app.name', 'Creative ERP'));
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date string or Carbon instance based on global settings.
     *
     * @param mixed $date
     * @param string $fallback
     * @return string|null
     */
    function format_date($date, string $fallback = 'M j, Y'): ?string
    {
        if (empty($date)) {
            return null;
        }

        try {
            $parsedDate = $date instanceof Carbon ? $date : Carbon::parse($date);
            $format = app(SettingsService::class)->get('date_format', $fallback);
            return $parsedDate->format($format);
        } catch (\Exception $e) {
            return (string)$date;
        }
    }
}

if (!function_exists('format_time')) {
    /**
     * Format a time string or Carbon instance based on global settings.
     *
     * @param mixed $time
     * @param string $fallback
     * @return string|null
     */
    function format_time($time, string $fallback = 'g:i A'): ?string
    {
        if (empty($time)) {
            return null;
        }

        try {
            $parsedTime = $time instanceof Carbon ? $time : Carbon::parse($time);
            $format = app(SettingsService::class)->get('time_format', $fallback);
            return $parsedTime->format($format);
        } catch (\Exception $e) {
            return (string)$time;
        }
    }
}

if (!function_exists('format_datetime')) {
    /**
     * Format a datetime string or Carbon instance based on global settings.
     *
     * @param mixed $datetime
     * @param string $dateFallback
     * @param string $timeFallback
     * @return string|null
     */
    function format_datetime($datetime, string $dateFallback = 'M j, Y', string $timeFallback = 'g:i A'): ?string
    {
        if (empty($datetime)) {
            return null;
        }

        try {
            $parsedDatetime = $datetime instanceof Carbon ? $datetime : Carbon::parse($datetime);
            $dateFormat = app(SettingsService::class)->get('date_format', $dateFallback);
            $timeFormat = app(SettingsService::class)->get('time_format', $timeFallback);
            
            return $parsedDatetime->format("{$dateFormat} {$timeFormat}");
        } catch (\Exception $e) {
            return (string)$datetime;
        }
    }
}

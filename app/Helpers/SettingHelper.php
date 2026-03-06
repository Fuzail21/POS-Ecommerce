<?php

use App\Models\Setting;

if (!function_exists('posSetting')) {
    function posSetting($key, $default = null) {
        static $setting = null;
        if ($setting === null) {
            $setting = \App\Models\Setting::first();
        }
        return $setting ? ($setting->{$key} ?? $default) : $default;
    }
}

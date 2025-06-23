<?php

use App\Models\Setting;

if (!function_exists('posSetting')) {
    function posSetting($key, $default = null) {
        $setting = Setting::first();
        return $setting->{$key} ?? $default;
    }
}

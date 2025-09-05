<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\SiteSetting;

class SettingService
{
    public function get($key, $default = null)
    {
        $record = Setting::where('key', $key)->first();
        return $record ? json_decode($record->value, true) : $default;
    }

    public function set($key, $value)
    {
        return Setting::updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($value)]
        );
    }
}

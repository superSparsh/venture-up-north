<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = "site_settings";

    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'json',
    ];

    public $timestamps = true;

    public static function get($key, $default = null)
    {
        return optional(self::where('key', $key)->first())->value ?? $default;
    }

    public static function set($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($value)]
        );
    }
}

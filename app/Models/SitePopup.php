<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SitePopup extends Model
{
    public $timestamps = false;
    protected $table = 'site_popups';
    protected $guarded = [];
    public $incrementing = false;
    protected $primaryKey = null;

    public static function single(): self
    {
        $row = static::query()->first();
        if (!$row) {
            DB::table('site_popups')->insert([
                'events_content' => '',
                'magazine_content' => '',
            ]);
            $row = static::query()->first();
        }
        return $row;
    }
}

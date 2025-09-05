<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourTilePageSetting extends Model
{
    protected $table = 'tour_tile_page_settings';

    protected $fillable = [
        'name',
        'summary',
        'description',
        'big_hero_image',
        'hero_image',
        'seo_title',
        'seo_description',
        'seo_image',
    ];
}

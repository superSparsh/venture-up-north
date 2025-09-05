<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionPageSetting extends Model
{
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

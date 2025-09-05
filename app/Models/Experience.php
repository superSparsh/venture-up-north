<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Experience extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'hero_image',
        'big_hero_image',
        'description',
        'rezdy_url',
        'seo_title',
        'seo_description',
        'seo_image',
        'is_active',
    ];

    protected $casts = [
        'description' => 'array',
        'is_active' => 'boolean',
    ];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }
}

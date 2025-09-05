<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TourTile extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'image',
        'big_hero_image',
        'is_active',
        'rezdy_url',
        'seo_title',
        'seo_description',
        'seo_image',
        'summary',
        'content',
        'location',
        'address',
        'opening_times',
        'email',
        'phone_number',
        'video',
        'custom_fields',
        'custom_buttons',
        'social_links',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_times' => 'array',
        'custom_fields' => 'array',
        'custom_buttons' => 'array',
        'social_links' => 'array',
    ];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_tour_tiles');
    }

    public function towns()
    {
        return $this->belongsToMany(Town::class, 'tour_tile_town');
    }
}

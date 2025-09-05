<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'hero_image',
        'big_hero_image',
        'summary',
        'description',
        'is_active',
        'seo_title',
        'seo_description',
        'type',
        'seo_image'
    ];

    public function items()
    {
        return $this->belongsToMany(ThingsToDoItem::class, 'collection_content_listings');
    }

    public function tours()
    {
        return $this->belongsToMany(TourTile::class, 'collection_tour_tiles');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'collection_events');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function towns()
    {
        return $this->belongsToMany(Town::class, 'collection_towns');
    }
}

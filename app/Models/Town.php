<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Town extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'hero_image',
        'big_hero_image',
        'summary',
        'description',
        'is_active',
        'rezdy_url',
        'rezdy_embed',
        'seo_title',
        'seo_description',
        'seo_image'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function categoryItems()
    {
        return $this->belongsToMany(ThingsToDoItem::class, 'category_item_town');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_town');
    }

    public function towns()
    {
        return $this->belongsToMany(TourTile::class, 'tour_tile_town');
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_towns');
    }
}

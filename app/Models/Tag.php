<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        // 'slug',
        // 'hero_image',
        // 'big_hero_image',
        // 'summary',
        // 'description',
        'is_active',
        // 'seo_title',
        // 'seo_description',
        // 'seo_image'
    ];

    public function towns()
    {
        return $this->morphedByMany(Town::class, 'taggable');
    }

    public function events()
    {
        return $this->morphedByMany(Event::class, 'taggable');
    }

    public function experiences()
    {
        return $this->morphedByMany(Experience::class, 'taggable');
    }

    public function tourTiles()
    {
        return $this->morphedByMany(TourTile::class, 'taggable');
    }

    public function magazines()
    {
        return $this->morphedByMany(VentureMagazine::class, 'taggable');
    }

    public function categories()
    {
        return $this->morphedByMany(ThingsToDoCategory::class, 'taggable');
    }

    public function categoryItems()
    {
        return $this->morphedByMany(ThingsToDoItem::class, 'taggable');
    }

    public function collections()
    {
        return $this->morphedByMany(Collection::class, 'taggable');
    }
}

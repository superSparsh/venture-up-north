<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThingsToDoItem extends Model
{
    protected $table = 'things_to_do_items';

    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'image',
        'big_image',
        'summary',
        'content',
        'is_active',
        'location',
        'address',
        'opening_times',
        'email',
        'phone_number',
        'video',
        'custom_fields',
        'custom_buttons',
        'social_links',
        'seo_title',
        'seo_description',
        'seo_image'
    ];

    protected $casts = [
        'content' => 'array',
        'opening_times' => 'array',
        'custom_fields' => 'array',
        'custom_buttons' => 'array',
        'social_links' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(ThingsToDoCategory::class, 'category_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function towns()
    {
        return $this->belongsToMany(Town::class, 'category_item_town');
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_content_listings');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'category_item_event');
    }
}

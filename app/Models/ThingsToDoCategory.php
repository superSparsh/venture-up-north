<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ThingsToDoCategory extends Model
{
    use HasFactory;

    protected $table = 'things_to_do_categories';

    protected $fillable = ['name', 'slug', 'icon', 'big_image', 'description', 'summary', 'is_active', 'seo_title', 'seo_description', 'seo_image'];

    public function items()
    {
        return $this->hasMany(ThingsToDoItem::class, 'category_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }
}

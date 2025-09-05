<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venture extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'title',
        'cover_image_url',
        'summary',
        'visibility',
        'status',
        'owner_user_id',
        'owner_guest_name',
        'owner_guest_email',
        'created_by_admin_id',
        'is_featured',
        'items_count',
        'days_count',
        'data_snapshot',
        'seo_title',
        'seo_description',
        'og_image_url',
        'canonical_url',
        'published_at',
        'owner_token'
    ];

    protected $casts = ['data_snapshot' => 'array', 'published_at' => 'datetime'];

    public function days()
    {
        return $this->hasMany(VentureDay::class)->orderBy('day_index');
    }
    public function items()
    {
        return $this->hasMany(VentureItem::class)->orderBy('position');
    }
}

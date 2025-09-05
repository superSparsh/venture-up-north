<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasApprovalWorkflow;

class Event extends Model
{
    use HasFactory;

    use HasApprovalWorkflow;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'hero_image',
        'big_hero_image',
        'summary',
        'content',
        'is_active',
        'location',
        'address',
        'opening_times',
        'email',
        'phone_number',
        'video',
        'status',
        'submitted_by',
        'custom_fields',
        'custom_buttons',
        'social_links',
        'event_date_label',
        'start_date',
        'end_date',
        'seo_title',
        'seo_description',
        'agreements',
        'seo_image'
    ];

    protected $casts = [
        'content' => 'array',
        'opening_times' => 'array',
        'custom_fields' => 'array',
        'custom_buttons' => 'array',
        'social_links' => 'array',
        'pending_payload' => 'array',
        'published_at'    => 'datetime',
        'agreements' => 'array',
    ];

    public function towns()
    {
        return $this->belongsToMany(Town::class, 'event_town');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_events');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('is_active', true)
            ->whereDate('end_date', '>=', now());
    }

    public function categoryItems()
    {
        return $this->belongsToMany(ThingsToDoItem::class, 'category_item_event');
    }

    /**
     * The user who created the event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

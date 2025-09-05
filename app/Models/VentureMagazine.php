<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasApprovalWorkflow;

class VentureMagazine extends Model
{
    use HasFactory;

    use HasApprovalWorkflow;

    protected $casts = [
        'pending_payload' => 'array',
        'published_at'    => 'datetime',
        'agreements' => 'array',
    ];

    protected $fillable = [
        'title',
        'contributor_id',
        'real_contributor_id',
        'hero_image',
        'big_hero_image',
        'content',
        'is_published',
        'is_featured',
        'seo_title',
        'seo_description',
        'seo_image',
        'published_at',
        'slug',
        'status',
        'agreements',
        'submitted_by'
    ];

    public function contributor()
    {
        return $this->belongsTo(TeamMember::class, 'contributor_id');
    }

    public function towns()
    {
        return $this->belongsToMany(Town::class, 'venture_magazine_towns');
    }

    public function experiences()
    {
        return $this->belongsToMany(Experience::class, 'venture_magazine_experiences');
    }

    public function tourTiles()
    {
        return $this->belongsToMany(TourTile::class, 'venture_magazine_tour_tiles');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable')->withTimestamps();
    }

    public function realContributor()
    {
        return $this->belongsTo(Contributor::class, 'real_contributor_id');
    }
}

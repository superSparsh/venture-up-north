<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'author_id',
        'status',          // 'active' or 'blocked'
        'blocked_reason',
        'blocked_at',
        'submissions_count',
        'approved_count',
        'rejected_count',
        'last_submission_at',
    ];

    /**
     * The linked user account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The author profile linked to this contributor.
     */
    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    /**
     * Check if contributor is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if contributor is blocked.
     */
    public function isBlocked(): bool
    {
        return $this->status === 'blocked';
    }

    public function magazines()
    {
        return $this->hasMany(VentureMagazine::class, 'real_contributor_id');
    }
}

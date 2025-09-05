<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'designation',
        'bio',
        'photo',
        'order',
        'is_active',
        'seo_title',
        'seo_description',
        'seo_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function magazines()
    {
        return $this->hasMany(VentureMagazine::class, 'contributor_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Author extends Model
{

    use HasFactory;

    protected $fillable = [
        'type',          // 'user' or 'team_member'
        'ref_id',        // user_id or team_member_id
        'display_name',
        'slug',
        'avatar_path',
    ];


    /**
     * A contributor may be linked to this author.
     */
    public function contributor()
    {
        return $this->hasOne(Contributor::class);
    }

    /**
     * Get the user if this author is of type 'user'.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'ref_id');
    }

    /**
     * Get the team member if this author is of type 'team_member'.
     */
    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class, 'ref_id');
    }

    /**
     * Posts or events written by this author.
     */
    public function magazines()
    {
        return $this->hasMany(VentureMagazine::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}

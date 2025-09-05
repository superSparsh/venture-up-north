<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExploreTile extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'source',
        'is_active',
        'sort_order',
    ];
}

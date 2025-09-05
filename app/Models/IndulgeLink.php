<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndulgeLink extends Model
{
    protected $fillable = ['title', 'url', 'is_active', 'sort_order'];
}

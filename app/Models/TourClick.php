<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TourClick extends Model
{
    protected $fillable = [
        'event_label',
        'click_count',
    ];
}

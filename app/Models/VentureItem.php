<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VentureItem extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'venture_id',
        'venture_day_id',
        'position',
        'item_type',
        'item_id',
        'source_url',
        'cat_source_url',
        'title',
        'image_url',
        'tags',
        'lat',
        'lng',
        'payload'
    ];
    protected $casts = ['tags' => 'array', 'payload' => 'array', 'lat' => 'float', 'lng' => 'float'];
    public function venture()
    {
        return $this->belongsTo(Venture::class);
    }
    public function day()
    {
        return $this->belongsTo(VentureDay::class, 'venture_day_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VentureDay extends Model
{
    use SoftDeletes;
    protected $fillable = ['venture_id', 'title', 'day_index'];
    public function venture()
    {
        return $this->belongsTo(Venture::class);
    }
    public function items()
    {
        return $this->hasMany(VentureItem::class);
    }
}

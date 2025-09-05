<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TermsConditionEvents extends Model
{
    protected $table = 'terms_conditions_events';
    protected $fillable = ['context', 'body', 'boxes', 'is_active', 'updated_by'];
    protected $casts = [
        'boxes' => 'array',
        'is_active' => 'boolean',
    ];

    public static function for(string $context = 'contributor_submission'): self|null
    {
        return static::where('context', $context)->where('is_active', true)->first();
    }
}

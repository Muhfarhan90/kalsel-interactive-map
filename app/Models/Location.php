<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Location extends Model
{
    protected $fillable = [
        'category_id',
        'regency_id',
        'name',
        'address',
        'description',
        'x_location',
        'y_location',
        'media',
        'is_active',
        'source_media',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }
}

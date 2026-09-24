<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourismLocation extends Model
{
    protected $fillable = [
        'category_id',
        'map_id',
        'location_name',
        'location_address',
        'location_description',
        'coordinate_x',
        'coordinate_y',
        'location_media_url',
        'location_source_media',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'coordinate_x' => 'float',
            'coordinate_y' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function tourism_category(): BelongsTo
    {
        return $this->belongsTo(TourismCategory::class, 'category_id');
    }

    public function tourism_map(): BelongsTo
    {
        return $this->belongsTo(TourismMap::class, 'map_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tourism_location extends Model
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

    public function tourism_category(): belongsTo
    {
        return $this->belongsTo(tourism_category::class, 'category_id');
    }

    public function tourism_map(): belongsTo
    {
        return $this->belongsTo(tourism_map::class, 'map_id');
    }
}

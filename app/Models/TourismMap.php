<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourismMap extends Model
{
    protected $fillable = [
        'map_title',
        'map_sub_title',
        'map_logo',
        'map_image',
        'map_description',
    ];

    public function tourism_locations(): HasMany
    {
        return $this->hasMany(TourismLocation::class, 'map_id');
    }

}

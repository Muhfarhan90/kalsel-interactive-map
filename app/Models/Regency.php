<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regency extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'map_image',
        'x_location',
        'y_location',
        'sort_order',
    ];

    protected function casts(): array
    {
        return ['x_location' => 'float', 'y_location' => 'float', 'sort_order' => 'integer'];
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}

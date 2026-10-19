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
    ];

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}

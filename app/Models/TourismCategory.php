<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourismCategory extends Model
{
    protected $fillable = [
        'category_name',
        'category_color',
        'category_icon',
        'category_description',
    ];

    public function tourism_locations(): HasMany
    {
        return $this->hasMany(TourismLocation::class, 'category_id');
    }
}

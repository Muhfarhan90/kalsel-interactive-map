<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tourism_map extends Model
{
    protected $fillable = [
        'map_title',
        'map_sub_title',
        'map_logo',
        'map_image',
        'map_description',
    ];

    public function tourism_spots(): hasMany
    {
        return $this->hasMany(tourism_spot::class, 'map_id');
    }

}

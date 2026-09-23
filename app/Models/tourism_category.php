<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class tourism_category extends Model
{
    protected $fillable = [
        'category_name',
        'category_color',
        'category_icon',
        'category_description',
    ];

    public function tourism_spots(): hasMany
    {
        return $this->hasMany(tourism_spot::class, 'cat_id');
    }
}

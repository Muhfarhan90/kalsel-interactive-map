<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homepage extends Model
{
    protected $fillable = [
        'title',
        'label',
        'description',
        'image',
        'header_title',
        'header_logo',
        'header_text',
        'color',
    ];


}

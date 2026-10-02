<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicPageHeader extends Model
{
    protected $fillable = [
        'page_key',
        'header_title',
        'header_logo',
        'header_logo_text',
    ];
}

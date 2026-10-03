<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportationCategory extends Model
{
    protected $fillable = [
        'category_name',
        'category_color',
        'category_icon',
        'category_description',
    ];

    public static function iconOptions(): array
    {
        return TourismCategory::iconOptions();
    }

    public function heroiconName(): string
    {
        if (array_key_exists($this->category_icon, self::iconOptions())) {
            return $this->category_icon;
        }

        return 'map-pin';
    }

    public function transportation_locations(): HasMany
    {
        return $this->hasMany(TransportationLocation::class, 'category_id');
    }
}

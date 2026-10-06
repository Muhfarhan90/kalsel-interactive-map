<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourismMap extends Model
{
    public const DEFAULT_BACKGROUND_TEXT = 'Jelajahi pesona alam dan budaya Kalimantan Selatan.';

    protected $fillable = [
        'map_title',
        'map_sub_title',
        'map_logo',
        'map_background_image',
        'map_background_text',
        'map_id',
    ];

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function getMapImageAttribute(): ?string
    {
        return $this->map?->map_image;
    }

    public function getMapDescriptionAttribute(): ?string
    {
        return $this->map?->map_description;
    }

    public function getMapBackgroundTextAttribute(?string $value): string
    {
        return $value ?: self::DEFAULT_BACKGROUND_TEXT;
    }

    public function tourism_locations(): HasMany
    {
        return $this->hasMany(TourismLocation::class, 'map_id');
    }

}

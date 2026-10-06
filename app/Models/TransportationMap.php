<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportationMap extends Model
{
    public const DEFAULT_BACKGROUND_TEXT = 'Temukan akses perjalanan untuk menjelajahi Kalimantan Selatan.';

    protected $fillable = ['map_id', 'map_title', 'map_sub_title', 'map_logo', 'map_background_image', 'map_background_text'];

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function transportation_locations(): HasMany
    {
        return $this->hasMany(TransportationLocation::class, 'map_id');
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
}

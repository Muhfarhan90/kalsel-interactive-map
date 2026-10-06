<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CulinaryMap extends Model
{
    public const DEFAULT_BACKGROUND_TEXT = 'Nikmati cita rasa khas Banua di setiap perjalanan.';

    protected $fillable = ['map_id', 'map_title', 'map_sub_title', 'map_logo', 'map_background_image', 'map_background_text'];

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function culinary_locations(): HasMany
    {
        return $this->hasMany(CulinaryLocation::class, 'map_id');
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

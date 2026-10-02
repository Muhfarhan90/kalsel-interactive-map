<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Map extends Model
{
    protected $fillable = ['map_image', 'map_description'];

    public static function shared(): self
    {
        $mapId = TourismMap::query()->latest('id')->value('map_id')
            ?? CulinaryMap::query()->latest('id')->value('map_id');

        return static::query()->find($mapId)
            ?? static::query()->latest('id')->first()
            ?? static::create([
                'map_image' => 'images/maps/peta_provinsi_kalsel.png',
                'map_description' => 'Peta wilayah Kalimantan Selatan.',
            ]);
    }

    public function tourism_maps(): HasMany
    {
        return $this->hasMany(TourismMap::class);
    }

    public function culinary_maps(): HasMany
    {
        return $this->hasMany(CulinaryMap::class);
    }
}

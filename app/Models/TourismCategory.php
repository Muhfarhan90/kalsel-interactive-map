<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    public static function iconOptions(): array
    {
        static $options;

        if ($options !== null) {
            return $options;
        }

        $paths = glob(base_path('vendor/blade-ui-kit/blade-heroicons/resources/svg/o-*.svg')) ?: [];
        $options = [];

        foreach ($paths as $path) {
            $name = substr(pathinfo($path, PATHINFO_FILENAME), 2);
            $options[$name] = Str::headline($name);
        }

        ksort($options);

        return $options;
    }

    public function heroiconName(): string
    {
        if (array_key_exists($this->category_icon, self::iconOptions())) {
            return $this->category_icon;
        }

        return 'map-pin';
    }
}

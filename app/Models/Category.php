<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'menu_id',
        'name',
        'icon',
        'color',
        'description',
        'background',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public static function iconOptions(): array
    {
        static $options = null;

        if ($options !== null) {
            return $options;
        }

        $paths = glob(
            base_path('vendor/fortawesome/font-awesome/svgs/solid/*.svg')
        ) ?: [];

        $options = [];

        foreach ($paths as $path) {
            $name = pathinfo($path, PATHINFO_FILENAME);

            $options[$name] = Str::headline($name);
        }

        ksort($options);

        return $options;
    }

    public function fontAwesomeClass(): string
    {
        $name = $this->icon;

        if (array_key_exists($name, self::iconOptions())) {
            return 'fa-solid fa-' . $name;
        }

        return 'fa-solid fa-location-dot';
    }
}

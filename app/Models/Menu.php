<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'title',
        'sub_title',
        'description',
        'logo',
        'banner',
        'color',
        'sort_order',
        'is_active',
    ];

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
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

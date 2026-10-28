<?php

namespace App\Http\Controllers;

use App\Models\Homepage;
use App\Models\Location;
use App\Models\Map;
use App\Models\Menu;
use App\Models\Regency;
use Illuminate\Support\Str;

class HomepageController extends Controller
{
    public function menu($slug)
    {
        $menu = Menu::where('slug', $slug)->firstOrFail();

        return view('pages.province', [
            'homepage' => Homepage::first(),
            'map' => Map::latest('id')->first(),
            'menu' => $menu,
            'regencies' => Regency::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function regencyMenu(Menu $menu, Regency $regency)
    {
        return $this->renderMenu($menu, $regency);
    }

    private function renderMenu(Menu $menu, Regency $regency)
    {
        $homepage = Homepage::first();

        $isDestinasi = $menu->slug === 'destinasi-pariwisata';

        $locations = Location::with('category')
            ->join('categories', 'locations.category_id', '=', 'categories.id')
            ->join('menus', 'categories.menu_id', '=', 'menus.id')
            ->where('locations.regency_id', $regency->id)
            ->where('locations.is_active', true)
            ->where('menus.is_active', true)
            ->when(! $isDestinasi, function ($query) use ($menu) {
                $query->where('menus.id', $menu->id);
            })
            ->select('locations.*')
            ->orderBy('menus.sort_order')
            ->orderBy('categories.sort_order')
            ->orderBy('locations.name')
            ->get()
            ->map(function (Location $location): array {
                $media = $location->media;

                if ($media && ! Str::startsWith($media, ['http://', 'https://'])) {
                    $media = asset(ltrim($media, '/'));
                }

                return [
                    'id' => $location->id,
                    'category_id' => $location->category_id,
                    'category' => $location->category->name,
                    'color' => $location->category->color,
                    'icon' => $location->category->icon,
                    'background' => $location->category->background
                        ? asset($location->category->background)
                        : null,
                    'name' => $location->name,
                    'address' => $location->address,
                    'description' => str_replace("\u{00A0}", ' ', $location->description ?? ''),
                    'media' => $media,
                    'medias' => $this->publicGallery($location),
                    'source_media' => $location->source_media,
                    'x_location' => $location->x_location,
                    'y_location' => $location->y_location,
                    'longitude' => $location->longitude,
                    'latitude' => $location->latitude,
                ];
            });

        return view('pages.menu', [
            'locations' => $locations,
            'menu' => $menu,
            'homepage' => $homepage,
            'regency' => $regency,
        ]);
    }

    public function allLocations()
    {
        $menu = new Menu([
            'name' => 'Peta Kalimantan Selatan',
            'slug' => 'destinasi-pariwisata',
            'title' => 'Peta Kalimantan Selatan',
            'icon' => 'map-location-dot',
            'description' => 'Jelajahi lokasi di seluruh Kalimantan Selatan',
            'color' => '#da251d',
            'logo' => 'images/logo/logo-wonderful-kalsel.png',
        ]);

        $locations = Location::query()
            ->with(['category.menu', 'regency'])
            ->join('categories', 'locations.category_id', '=', 'categories.id')
            ->join('menus', 'categories.menu_id', '=', 'menus.id')
            ->where('locations.is_active', true)
            ->where('menus.is_active', true)
            ->select('locations.*')
            ->orderBy('menus.sort_order')
            ->orderBy('categories.sort_order')
            ->orderBy('locations.name')
            ->get()
            ->map(function (Location $location): array {
                $media = $location->media;

                if ($media && ! Str::startsWith($media, ['http://', 'https://'])) {
                    $media = asset(ltrim($media, '/'));
                }

                return [
                    'id' => $location->id,
                    'menu_id' => $location->category->menu_id,
                    'menu' => $location->category->menu->name,
                    'category_id' => $location->category_id,
                    'category' => $location->category->name,
                    'color' => $location->category->color,
                    'icon' => $location->category->icon,
                    'background' => $location->category->background
                        ? asset(ltrim($location->category->background, '/'))
                        : null,
                    'regency' => $location->regency?->name,
                    'name' => $location->name,
                    'address' => $location->address,
                    'description' => str_replace("\u{00A0}", ' ', $location->description ?? ''),
                    'media' => $media,
                    'medias' => $this->publicGallery($location),
                    'source_media' => $location->source_media,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                ];
            });

        return view('pages.all-locations', [
            'locations' => $locations,
            'menu' => $menu,
            'homepage' => Homepage::first(),
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $homepage = Homepage::first();
        $menus = Menu::where('is_active', true)->where('slug', '!=', 'destinasi-wisata')
            ->orderBy('sort_order')->get();

        $destinasi = new Menu([
            'name' => 'Peta Kalimantan Selatan',
            'slug' => 'destinasi-pariwisata',
            'title' => 'Peta Kalimantan Selatan',
            'icon' => 'map-location-dot',
            'description' => 'Jelajahi keajaiban alam, pesona budaya, dan ragam destinasi ikonik di Kalimantan Selatan',
            'color' => '#da251d',
        ]);

        $menus->prepend($destinasi);

        return view('pages.home', compact('homepage', 'menus'));
    }

    private function publicGallery(Location $location): array
    {
        $gallery = $location->media_gallery ?: (
            $location->media ? [$location->media] : []
        );

        return collect($gallery)
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->unique()
            ->map(fn ($path) => Str::startsWith(
                $path, ['http://', 'https://']
            ) ? $path : asset(ltrim($path, '/')))
            ->values()
            ->all();
    }
}

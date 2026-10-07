<?php

namespace App\Http\Controllers;

use App\Models\Homepage;
use App\Models\Location;
use App\Models\Map;
use App\Models\Menu;
use App\Models\PublicPageHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomepageController extends Controller
{
    public function menu($slug)
    {
        $map = Map::latest('id')->first();

        $menu = Menu::where('slug', $slug)->with('categories')->firstOrFail();
        
        $locations = Location::with('category')
            ->join('categories', 'locations.category_id', '=', 'categories.id')
            ->join('menus', 'categories.menu_id', '=', 'menus.id')
            ->where('menus.id', $menu->id)
            // ->where('categories.is_active', true)
            ->select('locations.*')
            ->where('locations.is_active', true)
            // ->orderBy('categories.sort_order')
            ->orderBy('categories.id')
            ->orderBy('locations.name')
            ->get()
            ->map(function (Location $location): array {
                $media = $location->media;

                if ($media && ! Str::startsWith($media, ['http://', 'https://'])) {
                    $media = asset(ltrim($media, '/'));
                }

                return [
                    'id' => $location->id,
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
                    'source_media' => $location->source_media,
                    'x_location' => $location->x_location,
                    'y_location' => $location->y_location,
                ];
            });


        return view('pages.menu', [
            'locations' => $locations,
            'map' => $map,
            'menu' => $menu,
            'pageHeader' => PublicPageHeader::where('page_key', 'home')->firstOrFail(),
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pageHeader = PublicPageHeader::where('page_key', 'home')->firstOrFail();
        $homepage = Homepage::first();
        $menus = Menu::with('categories')->orderBy('sort_order')->get();
        return view('pages.home', compact('pageHeader', 'homepage', 'menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Homepage $homepage)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Homepage $homepage)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Homepage $homepage)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Homepage $homepage)
    {
        //
    }
}

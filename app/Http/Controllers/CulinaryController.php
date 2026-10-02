<?php

namespace App\Http\Controllers;

use App\Models\CulinaryLocation;
use App\Models\CulinaryMap;
use App\Models\PublicPageHeader;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CulinaryController extends Controller
{
    public function __invoke(): View
    {
        $map = CulinaryMap::with('map')->latest('id')->first();
        $locations = CulinaryLocation::with('culinary_category')
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('location_name')
            ->get()
            ->map(function (CulinaryLocation $location): array {
                $mediaUrl = $location->location_media_url;

                if ($mediaUrl && ! Str::startsWith($mediaUrl, ['http://', 'https://'])) {
                    $mediaUrl = asset(ltrim($mediaUrl, '/'));
                }

                return [
                    'id' => $location->id,
                    'category' => $location->culinary_category->category_name,
                    'category_color' => $location->culinary_category->category_color,
                    'category_icon_svg' => svg('heroicon-o-'.$location->culinary_category->heroiconName(), 'size-5')->toHtml(),
                    'location_name' => $location->location_name,
                    'location_address' => $location->location_address,
                    'location_description' => str_replace("\u{00A0}", ' ', $location->location_description ?? ''),
                    'location_media_url' => $mediaUrl,
                    'location_source_media' => $location->location_source_media,
                    'coordinate_x' => $location->coordinate_x,
                    'coordinate_y' => $location->coordinate_y,
                ];
            });

        return view('pages.culinary', [
            'culinaryLocations' => $locations,
            'map' => $map,
            'pageHeader' => PublicPageHeader::where('page_key', 'home')->firstOrFail(),
        ]);
    }
}

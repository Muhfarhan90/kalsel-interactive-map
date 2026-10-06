<?php

namespace App\Http\Controllers;

use App\Models\TransportationLocation;
use App\Models\TransportationMap;
use App\Models\PublicPageHeader;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TransportationController extends Controller
{
    public function __invoke(): View
    {
        $map = TransportationMap::with('map')->latest('id')->first();
        $locations = TransportationLocation::with('transportation_category')
            ->join('transportation_categories', 'transportation_locations.category_id', '=', 'transportation_categories.id')
            ->select('transportation_locations.*')
            ->where('transportation_locations.is_active', true)
            ->orderBy('transportation_categories.sort_order')
            ->orderBy('transportation_categories.id')
            ->orderBy('transportation_locations.location_name')
            ->get()
            ->map(function (TransportationLocation $location): array {
                $mediaUrl = $location->location_media_url;

                if ($mediaUrl && ! Str::startsWith($mediaUrl, ['http://', 'https://'])) {
                    $mediaUrl = asset(ltrim($mediaUrl, '/'));
                }

                return [
                    'id' => $location->id,
                    'category' => $location->transportation_category->category_name,
                    'category_color' => $location->transportation_category->category_color,
                    'category_icon_svg' => svg('heroicon-o-'.$location->transportation_category->heroiconName(), 'size-5')->toHtml(),
                    'category_background_image' => $location->transportation_category->category_background_image
                        ? asset($location->transportation_category->category_background_image)
                        : null,
                    'location_name' => $location->location_name,
                    'location_address' => $location->location_address,
                    'location_description' => str_replace("\u{00A0}", ' ', $location->location_description ?? ''),
                    'location_media_url' => $mediaUrl,
                    'location_source_media' => $location->location_source_media,
                    'coordinate_x' => $location->coordinate_x,
                    'coordinate_y' => $location->coordinate_y,
                ];
            });

        return view('pages.transportation', [
            'transportationLocations' => $locations,
            'map' => $map,
            'pageHeader' => PublicPageHeader::where('page_key', 'home')->firstOrFail(),
        ]);
    }
}

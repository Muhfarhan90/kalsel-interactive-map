<?php

namespace App\Http\Controllers;

use App\Models\TourismLocation;
use App\Models\TourismMap;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TourismController extends Controller
{
    public function __invoke(): View
    {
        $map = TourismMap::first();
        $locations = TourismLocation::with('tourism_category')
            ->where('is_active', true)
            ->orderBy('category_id')
            ->orderBy('location_name')
            ->get()
            ->map(function (TourismLocation $location): array {
                $mediaUrl = $location->location_media_url;

                if ($mediaUrl && ! Str::startsWith($mediaUrl, ['http://', 'https://'])) {
                    $mediaUrl = asset(ltrim($mediaUrl, '/'));
                }

                return [
                    'id' => $location->id,
                    'category' => $location->tourism_category->category_name,
                    'category_color' => $location->tourism_category->category_color,
                    'category_icon_svg' => svg('heroicon-o-'.$location->tourism_category->heroiconName(), 'size-5')->toHtml(),
                    'location_name' => $location->location_name,
                    'location_address' => $location->location_address,
                    'location_description' => str_replace("\u{00A0}", ' ', $location->location_description ?? ''),
                    'location_media_url' => $mediaUrl,
                    'location_source_media' => $location->location_source_media,
                    'coordinate_x' => $location->coordinate_x,
                    'coordinate_y' => $location->coordinate_y,
                ];
            });

        return view('pages.tourism', [
            'tourismLocations' => $locations,
            'map' => $map,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Map;
use App\Models\TourismMap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TourismMapController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.map.edit', [
            'map' => $this->map(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $tourismMap = $this->map();
        $data = $request->validate([
            'map_title' => ['required', 'string', 'max:255'],
            'map_sub_title' => ['nullable', 'string', 'max:255'],
            'map_background_text' => ['nullable', 'string', 'max:150'],
            'map_logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'map_background_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'remove_map_background_image' => ['nullable', 'boolean'],
        ]);

        $previousBackgroundImage = $tourismMap->map_background_image;
        $backgroundImage = $request->hasFile('map_background_image_file')
            ? $this->storeImage($request->file('map_background_image_file'), null)
            : ($request->boolean('remove_map_background_image') ? null : $previousBackgroundImage);

        $tourismMap->update([
            'map_title' => $data['map_title'],
            'map_sub_title' => $data['map_sub_title'] ?? null,
            'map_background_text' => $data['map_background_text'] ?? null,
            'map_logo' => $request->hasFile('map_logo_file')
                ? $this->storeImage($request->file('map_logo_file'), $tourismMap->map_logo)
                : $tourismMap->map_logo,
            'map_background_image' => $backgroundImage,
        ]);

        if ($previousBackgroundImage !== $backgroundImage && $previousBackgroundImage && str_starts_with($previousBackgroundImage, 'storage/')) {
            Storage::disk('public')->delete(substr($previousBackgroundImage, 8));
        }

        return back()->with('success', 'Pengaturan peta berhasil diperbarui.');
    }

    private function map(): TourismMap
    {
        $tourismMap = TourismMap::with('map')->latest('id')->first();
        if ($tourismMap) {
            if (!$tourismMap->map) {
                $tourismMap->map()->associate(Map::shared())->save();
            }

            return $tourismMap->load('map');
        }

        return TourismMap::create([
            'map_id' => Map::shared()->id,
            'map_title' => 'Peta Wisata Kalimantan Selatan',
            'map_sub_title' => 'Interactive Map Guidance',
            'map_logo' => 'images/logo/logo_kalsel.svg',
        ])->load('map');
    }

    private function storeImage(UploadedFile $file, ?string $currentPath): string
    {
        $path = $file->storeAs('maps', Str::uuid().'.'.strtolower($file->extension()), 'public');

        if ($currentPath && str_starts_with($currentPath, 'storage/')) {
            Storage::disk('public')->delete(substr($currentPath, 8));
        }

        return 'storage/'.$path;
    }

}

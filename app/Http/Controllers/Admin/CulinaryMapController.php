<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CulinaryMap;
use App\Models\Map;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CulinaryMapController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.culinary-map.edit', ['map' => $this->map()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $culinaryMap = $this->map();
        $data = $request->validate([
            'map_title' => ['required', 'string', 'max:255'],
            'map_sub_title' => ['nullable', 'string', 'max:255'],
            'map_background_text' => ['nullable', 'string', 'max:150'],
            'map_logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'map_background_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'remove_map_background_image' => ['nullable', 'boolean'],
        ]);

        $previousBackgroundImage = $culinaryMap->map_background_image;
        $backgroundImage = $request->hasFile('map_background_image_file')
            ? $this->storeImage($request->file('map_background_image_file'), null)
            : ($request->boolean('remove_map_background_image') ? null : $previousBackgroundImage);

        $culinaryMap->update([
            'map_title' => $data['map_title'],
            'map_sub_title' => $data['map_sub_title'] ?? null,
            'map_background_text' => $data['map_background_text'] ?? null,
            'map_logo' => $request->hasFile('map_logo_file')
                ? $this->storeImage($request->file('map_logo_file'), $culinaryMap->map_logo)
                : $culinaryMap->map_logo,
            'map_background_image' => $backgroundImage,
        ]);

        if ($previousBackgroundImage !== $backgroundImage && $previousBackgroundImage && str_starts_with($previousBackgroundImage, 'storage/')) {
            Storage::disk('public')->delete(substr($previousBackgroundImage, 8));
        }

        return back()->with('success', 'Pengaturan peta kuliner berhasil diperbarui.');
    }

    private function map(): CulinaryMap
    {
        $culinaryMap = CulinaryMap::with('map')->latest('id')->first();
        if ($culinaryMap) {
            if (!$culinaryMap->map) {
                $culinaryMap->map()->associate(Map::shared())->save();
            } elseif ($culinaryMap->map_id !== Map::shared()->id) {
                $culinaryMap->map()->associate(Map::shared())->save();
            }

            return $culinaryMap->load('map');
        }

        return CulinaryMap::create([
            'map_id' => Map::shared()->id,
            'map_title' => 'Peta Kuliner Kalimantan Selatan',
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

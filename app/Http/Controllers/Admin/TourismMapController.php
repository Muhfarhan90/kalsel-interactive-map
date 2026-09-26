<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourismMap;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TourismMapController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.map.edit', ['map' => $this->map()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $map = $this->map();
        $data = $request->validate([
            'map_title' => ['required', 'string', 'max:255'],
            'map_sub_title' => ['nullable', 'string', 'max:255'],
            'map_description' => ['nullable', 'string', 'max:2000'],
            'header_title' => ['required', 'string', 'max:255'],
            'header_sub_title' => ['required', 'string', 'max:255'],
            'map_logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'map_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        foreach (['map_logo_file' => ['map_logo', 'logo'], 'map_image_file' => ['map_image', 'map']] as $input => [$column, $name]) {
            if ($request->hasFile($input)) {
                $data[$column] = $this->storeImage($request->file($input), $name, $map->{$column});
            }
            unset($data[$input]);
        }

        $map->update($data);

        return back()->with('success', 'Pengaturan peta berhasil diperbarui.');
    }

    private function map(): TourismMap
    {
        return TourismMap::firstOrCreate(
            ['map_title' => 'Peta Wisata Kalimantan Selatan'],
            [
                'map_sub_title' => 'Interactive Map Guidance',
                'map_logo' => 'images/logo/logo_kalsel.svg',
                'map_image' => 'images/maps/peta_provinsi_kalsel.png',
                'map_description' => 'Peta utama lokasi wisata Kalimantan Selatan.',
                'header_title' => 'Kalimantan Selatan',
                'header_sub_title' => 'Interactive Map Guidance',
            ],
        );
    }

    private function storeImage(UploadedFile $file, string $name, ?string $currentPath): string
    {
        $path = 'maps/'.$name.'.'.strtolower($file->extension());

        if ($currentPath && str_starts_with($currentPath, 'storage/') && $currentPath !== 'storage/'.$path) {
            Storage::disk('public')->delete(substr($currentPath, 8));
        }

        $file->storeAs('maps', basename($path), 'public');

        return 'storage/'.$path;
    }

}

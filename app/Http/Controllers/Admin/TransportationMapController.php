<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportationMap;
use App\Models\Map;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class TransportationMapController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.transportation-map.edit', ['map' => $this->map()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $transportationMap = $this->map();
        $data = $request->validate([
            'map_title' => ['required', 'string', 'max:255'],
            'map_sub_title' => ['nullable', 'string', 'max:255'],
            'map_logo_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $transportationMap->update([
            'map_title' => $data['map_title'],
            'map_sub_title' => $data['map_sub_title'] ?? null,
            'map_logo' => $request->hasFile('map_logo_file')
                ? $this->storeImage($request->file('map_logo_file'), $transportationMap->map_logo)
                : $transportationMap->map_logo,
        ]);

        return back()->with('success', 'Pengaturan peta transportasi berhasil diperbarui.');
    }

    private function map(): TransportationMap
    {
        $transportationMap = TransportationMap::with('map')->latest('id')->first();
        if ($transportationMap) {
            if (!$transportationMap->map) {
                $transportationMap->map()->associate(Map::shared())->save();
            } elseif ($transportationMap->map_id !== Map::shared()->id) {
                $transportationMap->map()->associate(Map::shared())->save();
            }

            return $transportationMap->load('map');
        }

        return TransportationMap::create([
            'map_id' => Map::shared()->id,
            'map_title' => 'Peta Transportasi Kalimantan Selatan',
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

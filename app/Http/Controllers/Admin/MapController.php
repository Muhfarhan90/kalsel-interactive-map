<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Map;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MapController extends Controller
{
    public function edit(): View
    {
        return view('pages.admin.maps.edit', ['map' => Map::shared()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $map = Map::shared();
        $data = $request->validate([
            'map_description' => ['nullable', 'string', 'max:2000'],
            'map_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $data['map_description'] = $data['map_description'] ?? null;
        if ($request->hasFile('map_image_file')) {
            $data['map_image'] = $this->storeImage($request->file('map_image_file'), $map->map_image);
        }
        unset($data['map_image_file']);

        $map->update($data);

        return back()->with('success', 'Pengaturan peta berhasil diperbarui.');
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

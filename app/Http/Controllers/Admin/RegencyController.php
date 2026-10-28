<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Map;
use App\Models\Regency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegencyController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.regencies.index', [
            'regencies' => Regency::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.regencies.form', ['regency' => new Regency, 'map' => Map::latest('id')->first()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $regency = new Regency([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'x_location' => $data['x_location'] ?? null,
            'y_location' => $data['y_location'] ?? null,
            'sort_order' => (int) Regency::max('sort_order') + 1,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        if ($request->hasFile('map_image_file')) {
            $regency->map_image = $this->storeMap($request->file('map_image_file'), $regency);
        }

        $regency->save();

        return redirect()->route('admin.regencies.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function edit(Regency $regency): View
    {
        return view('pages.admin.regencies.form', ['regency' => $regency, 'map' => Map::latest('id')->first()]);
    }

    public function update(Request $request, Regency $regency): RedirectResponse
    {
        $data = $this->validatedData($request, $regency);
        $previousMap = $regency->map_image;
        $regency->name = $data['name'];
        $regency->latitude = $data['latitude'] ?? null;
        $regency->longitude = $data['longitude'] ?? null;

        if ($request->hasFile('map_image_file')) {
            $regency->map_image = $this->storeMap($request->file('map_image_file'), $regency);
        }

        $regency->save();

        if ($request->hasFile('map_image_file') && $previousMap === $regency->map_image) {
            $regency->touch();
        }

        if ($previousMap !== $regency->map_image) {
            $this->deleteStoredMap($previousMap);
        }

        return redirect()->route('admin.regencies.index')->with('success', 'Wilayah berhasil diperbarui.');
    }

    public function destroy(Regency $regency): RedirectResponse
    {
        if ($regency->locations()->exists()) {
            return redirect()->route('admin.regencies.index')->with('error', 'Wilayah masih digunakan oleh lokasi dan tidak dapat dihapus.');
        }

        $map = $regency->map_image;
        $regency->delete();
        $this->deleteStoredMap($map);

        return redirect()->route('admin.regencies.index')->with('success', 'Wilayah berhasil dihapus.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'distinct'],
        ])['order'];

        $ids = array_map('intval', $order);
        $submittedIds = $ids;
        $existingIds = Regency::pluck('id')->map(fn ($id) => (int) $id)->all();
        sort($submittedIds);
        sort($existingIds);

        if ($submittedIds !== $existingIds) {
            return back()->with('error', 'Daftar wilayah berubah. Muat ulang halaman dan coba lagi.');
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $position => $id) {
                Regency::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        return back()->with('success', 'Urutan wilayah berhasil diperbarui.');
    }

    private function validatedData(Request $request, ?Regency $regency = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', Rule::unique('regencies', 'name')->ignore($regency?->id)],
            'x_location' => ['nullable', 'required_with:y_location', 'numeric', 'between:0,100'],
            'y_location' => ['nullable', 'required_with:x_location', 'numeric', 'between:0,100'],
            'map_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'latitude' => ['nullable', 'required_with:longitude', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'required_with:latitude', 'numeric', 'between:-180,180'],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = preg_replace('/^(kabupaten|kota)-/', '', Str::slug($name)) ?: 'wilayah';
        $slug = $base;
        $number = 2;

        while (Regency::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$number}";
            $number++;
        }

        return $slug;
    }

    private function storeMap(UploadedFile $file, Regency $regency): string
    {
        return 'storage/'.$file->storeAs('maps/regencies', $regency->slug.'.'.$file->extension(), 'public');
    }

    private function deleteStoredMap(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/maps/regencies/')) {
            Storage::disk('public')->delete(substr($path, strlen('storage/')));
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Regency;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegencyController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.regencies.index', [
            'regencies' => Regency::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.regencies.form', ['regency' => new Regency()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $regency = new Regency([
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
        ]);

        if ($request->hasFile('map_image_file')) {
            $regency->map_image = $this->storeMap($request->file('map_image_file'), $regency);
        }

        $regency->save();

        return redirect()->route('admin.regencies.index')->with('success', 'Wilayah berhasil ditambahkan.');
    }

    public function edit(Regency $regency): View
    {
        return view('pages.admin.regencies.form', compact('regency'));
    }

    public function update(Request $request, Regency $regency): RedirectResponse
    {
        $data = $this->validatedData($request, $regency);
        $previousMap = $regency->map_image;
        $regency->name = $data['name'];

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

    private function validatedData(Request $request, ?Regency $regency = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', Rule::unique('regencies', 'name')->ignore($regency?->id)],
            'map_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'wilayah';
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

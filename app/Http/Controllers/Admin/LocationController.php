<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Map;
use App\Models\Menu;
use App\Models\Regency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $menu = $this->menuFromRequest($request);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->where('menu_id', $menu->id)],
            'regency_id' => ['nullable', 'integer', Rule::exists('regencies', 'id')],
        ]);

        $search = trim($filters['search'] ?? '');
        $categoryId = $filters['category_id'] ?? null;
        $regencyId = $filters['regency_id'] ?? null;

        $locations = Location::query()
            ->with(['category', 'regency'])
            ->whereHas('category', fn ($query) => $query->where('menu_id', $menu->id))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            }))
            ->when($categoryId !== null, fn ($query) => $query->where('category_id', $categoryId))
            ->when($regencyId !== null, fn ($query) => $query->where('regency_id', $regencyId))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.locations.index', [
            'menu' => $menu,
            'locations' => $locations,
            'categories' => $menu->categories()->orderBy('sort_order')->orderBy('name')->get(),
            'regencies' => Regency::orderBy('sort_order')->orderBy('id')->get(),
            'search' => $search,
            'categoryId' => $categoryId,
            'regencyId' => $regencyId,
        ]);
    }

    public function create(Request $request): View
    {
        return $this->formView(new Location, $this->menuFromRequest($request));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $menu = $this->menuFromRequest($request);
        $data = $this->validatedData($request, $menu);
        $uploaded = [];

        try {
            $gallery = $this->uploadGallery(
                $request->file('gallery_files', []),
                $uploaded
            );

            $data['media_gallery'] = $gallery;
            $data['media'] = $gallery[0] ?? null;

            Location::create($data);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($uploaded);
            throw $e;
        }

        return $this->savedResponse($request, $menu, 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location): View
    {
        $location->loadMissing('category.menu');

        return $this->formView($location, $location->category->menu);
    }

    public function update(Request $request, Location $location): RedirectResponse|JsonResponse
    {
        $location->loadMissing('category.menu');

        $menu = $location->category->menu;
        $data = $this->validatedData($request, $menu);

        $gallery = $this->currentGallery($location);
        $remove = $request->input('gallery_remove', []);

        // Memastikan path yang dihapus itu milik lokasi ini.
        if (array_diff($remove, $gallery)) {
            throw ValidationException::withMessages(['gallery_remove' => 'Media yang dihapus tidak valid.']);
        }

        $deleted = array_values(array_intersect($gallery, $remove));
        $remaining = array_values(array_diff($gallery, $deleted));

        // Mengurutkan media berdasarkan posisi card di form.
        if ($request->has('gallery_order')) {
            $order = $request->input('gallery_order', []);

            // Memastikan semua path memang milik lokasi ini.
            if (
                count($order) !== count($gallery) ||
                array_diff($order, $gallery) ||
                array_diff($gallery, $order)
            ) {
                throw ValidationException::withMessages([
                    'gallery_order' => 'Urutan media tidak valid.',
                ]);
            }

            // Mempertahankan urutan baru, kecuali media yang dihapus.
            $remaining = array_values(array_intersect($order, $remaining));
        }

        $uploaded = [];

        try {
            $newMedia = $this->uploadGallery(
                $request->file('gallery_files', []),
                $uploaded
            );

            $gallery = array_values(array_unique([
                ...$remaining,
                ...$newMedia,
            ]));

            $data['media_gallery'] = $gallery;
            $data['media'] = $gallery[0] ?? null;

            $location->update($data);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($uploaded);
            throw $e;
        }

        foreach ($deleted as $path) {
            $this->deleteStoredMedia($path);
        }

        return $this->savedResponse($request, $menu, 'Lokasi berhasil diperbarui.');
    }

    public function reorderGallery(Request $request, Location $location): JsonResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array', 'min:1'],
            'order.*' => ['required', 'string', 'distinct'],
        ]);

        $gallery = $this->currentGallery($location);
        $order = $data['order'];

        // Pastikan jumlah dan seluruh path sesuai media milik lokasi.
        if (
            count($order) !== count($gallery) ||
            array_diff($order, $gallery) ||
            array_diff($gallery, $order)
        ) {
            throw ValidationException::withMessages([
                'order' => 'Urutan media tidak valid.',
            ]);
        }

        $location->update([
            'media_gallery' => $order,
            'media' => $order[0] ?? null,
        ]);

        return response()->json([
            'message' => 'Urutan media berhasil disimpan.',
        ]);
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->loadMissing('category.menu');

        $menu = $location->category->menu;

        $paths = array_unique([
            ...$this->currentGallery($location),
            ...array_filter([$location->media]),
        ]);

        $location->delete();

        foreach ($paths as $media) {
            $this->deleteStoredMedia($media);
        }

        return redirect()
            ->route('admin.locations.index', ['menu' => $menu->slug])
            ->with('success', 'Lokasi berhasil dihapus.');
    }

    private function formView(Location $location, Menu $menu): View
    {
        return view('pages.admin.locations.form', [
            'menu' => $menu,
            'location' => $location,
            'categories' => $menu->categories()->orderBy('sort_order')->orderBy('name')->get(),
            'map' => Map::shared(),
            'regencies' => Regency::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    private function savedResponse(Request $request, Menu $menu, string $message): RedirectResponse|JsonResponse
    {
        $url = route('admin.locations.index', ['menu' => $menu->slug]);

        if ($request->expectsJson()) {
            $request->session()->flash('success', $message);

            return response()->json(['redirect' => $url]);
        }

        return redirect()->to($url)->with('success', $message);
    }

    private function menuFromRequest(Request $request): Menu
    {
        return Menu::query()->where('slug', $request->query('menu'))->firstOrFail();
    }

    private function validatedData(Request $request, Menu $menu): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('menu_id', $menu->id)],
            'regency_id' => ['nullable', 'integer', Rule::exists('regencies', 'id')],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'x_location' => ['nullable', 'numeric', 'between:0,100'],
            'y_location' => ['nullable', 'numeric', 'between:0,100'],
            'source_media' => ['nullable', 'string', 'max:255'],
            'gallery_files' => ['nullable', 'array'],
            'gallery_files.*' => [
                'required', 'file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:71680',
            ],
            'gallery_remove' => ['nullable', 'array'],
            'gallery_remove.*' => ['required', 'string', 'distinct', 'max:2048'],
            'gallery_order' => ['sometimes', 'array'],
            'gallery_order.*' => ['required', 'string', 'distinct'],
            'is_active' => ['nullable', 'boolean'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
        ]);

        unset($data['gallery_files'], $data['gallery_remove'], $data['gallery_order']);
        $data['is_active'] = $request->boolean('is_active');

        if (! empty($data['description'])) {
            $config = new HtmlSanitizerConfig();

            foreach (['p', 'h1', 'h2', 'h3', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'blockquote'] as $tag) {
                $config = $config->allowElement($tag, []);
            }

            $data['description'] = str_replace("\u{00A0}", ' ', (new HtmlSanitizer($config))->sanitize($data['description']));
        }

        return $data;
    }

    private function deleteStoredMedia(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/menus/locations/')) {
            Storage::disk('public')->delete(substr($path, strlen('storage/')));
        }
    }

    private function currentGallery(Location $location): array
    {
        $gallery = $location->media_gallery ?: (
            $location->media ? [$location->media] : []
        );

        return collect($gallery)
            ->filter(fn ($path) => is_string($path) && $path !== '')
            ->unique()
            ->values()
            ->all();
    }

    private function uploadGallery(array $files, array $uploaded): array
    {
        $gallery = [];

        foreach ($files as $file) {
            $path = $file->store('menus/locations', 'public');

            if (! $path) {
                throw new \RuntimeException('Gagal menyimpan media');
            }

            $uploaded[] = $path;
            $gallery[] = 'storage/'.$path;
        }

        return $gallery;
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Map;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
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
        ]);

        $search = trim($filters['search'] ?? '');
        $categoryId = $filters['category_id'] ?? null;

        $locations = Location::query()
            ->with('category')
            ->whereHas('category', fn ($query) => $query->where('menu_id', $menu->id))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            }))
            ->when($categoryId !== null, fn ($query) => $query->where('category_id', $categoryId))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.locations.index', [
            'menu' => $menu,
            'locations' => $locations,
            'categories' => $menu->categories()->orderBy('sort_order')->orderBy('name')->get(),
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }

    public function create(Request $request): View
    {
        return $this->formView(new Location(), $this->menuFromRequest($request));
    }

    public function store(Request $request): RedirectResponse
    {
        $menu = $this->menuFromRequest($request);
        $data = $this->validatedData($request, $menu);

        if ($request->hasFile('media_file')) {
            $data['media'] = 'storage/'.$request->file('media_file')->store('menus/locations', 'public');
        }

        Location::create($data);

        return redirect()
            ->route('admin.locations.index', ['menu' => $menu->slug])
            ->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function edit(Location $location): View
    {
        $location->loadMissing('category.menu');

        return $this->formView($location, $location->category->menu);
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $location->loadMissing('category.menu');
        $menu = $location->category->menu;
        $data = $this->validatedData($request, $menu);
        $previousMedia = $location->media;

        if ($request->hasFile('media_file')) {
            $data['media'] = 'storage/'.$request->file('media_file')->store('menus/locations', 'public');
        } elseif ($request->boolean('remove_media')) {
            $data['media'] = null;
        }

        $location->update($data);

        if ($previousMedia !== $location->media) {
            $this->deleteStoredMedia($previousMedia);
        }

        return redirect()
            ->route('admin.locations.index', ['menu' => $menu->slug])
            ->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->loadMissing('category.menu');
        $menu = $location->category->menu;
        $media = $location->media;

        $location->delete();
        $this->deleteStoredMedia($media);

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
        ]);
    }

    private function menuFromRequest(Request $request): Menu
    {
        return Menu::query()->where('slug', $request->query('menu'))->firstOrFail();
    }

    private function validatedData(Request $request, Menu $menu): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')->where('menu_id', $menu->id)],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'x_location' => ['required', 'numeric', 'between:0,100'],
            'y_location' => ['required', 'numeric', 'between:0,100'],
            'source_media' => ['nullable', 'string', 'max:255'],
            'media_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:71680'],
            'is_active' => ['nullable', 'boolean'],
            'remove_media' => ['nullable', 'boolean'],
        ]);

        unset($data['media_file'], $data['remove_media']);
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
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourismCategory;
use App\Models\TourismLocation;
use App\Models\TourismMap;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class TourismLocationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:tourism_categories,id'],
        ]);

        $search = $filters['search'] ?? '';
        $categoryId = $filters['category_id'] ?? '';

        $locations = TourismLocation::with('tourism_category')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('location_name', 'like', "%{$search}%")
                        ->orWhere('location_address', 'like', "%{$search}%");
                });
            })
            ->when($categoryId !== '', fn ($query) => $query->where('category_id', $categoryId))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.locations.index', [
            'locations' => $locations,
            'categories' => TourismCategory::orderBy('category_name')->get(['id', 'category_name']),
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }

    public function create(): View
    {
        return $this->formView(new TourismLocation());
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        TourismLocation::create($this->validatedData($request));
        $message = 'Data wisata berhasil ditambahkan.';

        if ($request->expectsJson()) {
            $request->session()->flash('success', $message);

            return response()->json(['redirect' => route('admin.locations.index')]);
        }

        return redirect()
            ->route('admin.locations.index')
            ->with('success', $message);
    }

    public function edit(TourismLocation $location): View
    {
        return $this->formView($location);
    }

    public function update(Request $request, TourismLocation $location): RedirectResponse|JsonResponse
    {
        $data = $this->validatedData($request, $location);
        $location->update($data);
        $message = 'Data wisata berhasil diperbarui.';

        if ($request->expectsJson()) {
            $request->session()->flash('success', $message);

            return response()->json(['redirect' => route('admin.locations.index')]);
        }

        return redirect()
            ->route('admin.locations.index')
            ->with('success', $message);
    }

    public function destroy(TourismLocation $location): RedirectResponse
    {
        $this->deleteStoredMedia($location->location_media_url);
        $location->delete();

        return back()->with('success', 'Data wisata berhasil dihapus.');
    }

    public function destroyMedia(TourismLocation $location): RedirectResponse
    {
        $this->deleteStoredMedia($location->location_media_url);
        $location->update(['location_media_url' => null]);

        return redirect()
            ->route('admin.locations.edit', $location)
            ->with('success', 'Media wisata berhasil dihapus.');
    }

    private function formView(TourismLocation $location): View
    {
        return view('pages.admin.locations.form', [
            'location' => $location,
            'categories' => TourismCategory::orderBy('category_name')->get(),
            'map' => $this->defaultMap(),
        ]);
    }

    private function validatedData(Request $request, ?TourismLocation $location = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:tourism_categories,id'],
            'location_name' => ['required', 'string', 'max:150'],
            'location_address' => ['required', 'string', 'max:255'],
            'location_description' => ['required', 'string'],
            'coordinate_x' => ['required', 'numeric', 'between:0,100'],
            'coordinate_y' => ['required', 'numeric', 'between:0,100'],
            'location_source_media' => ['nullable', 'string', 'max:255'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:71680'],
        ]);

        $media = $data['media'] ?? null;
        unset($data['media']);

        $config = new HtmlSanitizerConfig();
        foreach (['p', 'h1', 'h2', 'h3', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'blockquote'] as $tag) {
            $config = $config->allowElement($tag, []);
        }
        $data['location_description'] = str_replace("\u{00A0}", ' ', (new HtmlSanitizer($config))->sanitize($data['location_description']));

        $data['is_active'] = $request->boolean('is_active');
        $data['map_id'] = $this->defaultMap()->id;

        if ($media) {
            $this->deleteStoredMedia($location?->location_media_url);

            $name = Str::slug($data['location_name']) ?: 'wisata';
            $extension = strtolower($media->extension());
            $filename = $name.'.'.$extension;
            $suffix = 2;

            while (Storage::disk('public')->exists('tourism/'.$filename)) {
                $filename = $name.'-'.$suffix++.'.'.$extension;
            }

            $data['location_media_url'] = 'storage/'.$media->storeAs('tourism', $filename, 'public');
        }

        return $data;
    }

    private function defaultMap(): TourismMap
    {
        return TourismMap::firstOrCreate(
            ['map_title' => 'Peta Wisata Kalimantan Selatan'],
            [
                'map_sub_title' => 'Interactive Map Guidance',
                'map_logo' => 'images/logo/logo_kalsel.svg',
                'map_image' => 'images/maps/peta_provinsi_kalsel.png',
                'map_description' => 'Peta utama lokasi wisata Kalimantan Selatan.',
            ],
        );
    }

    private function deleteStoredMedia(?string $mediaUrl): void
    {
        if ($mediaUrl && str_starts_with($mediaUrl, 'storage/')) {
            Storage::disk('public')->delete(substr($mediaUrl, 8));
        }
    }
}

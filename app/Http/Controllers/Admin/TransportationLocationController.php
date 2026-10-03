<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportationCategory;
use App\Models\TransportationLocation;
use App\Models\TransportationMap;
use App\Models\Map;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class TransportationLocationController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:transportation_categories,id'],
        ]);
        $search = $filters['search'] ?? '';
        $categoryId = $filters['category_id'] ?? '';

        return view('pages.admin.transportation-locations.index', [
            'locations' => TransportationLocation::with('transportation_category')
                ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                    $query->where('location_name', 'like', "%{$search}%")
                        ->orWhere('location_address', 'like', "%{$search}%");
                }))
                ->when($categoryId !== '', fn ($query) => $query->where('category_id', $categoryId))
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'categories' => TransportationCategory::orderBy('category_name')->get(['id', 'category_name']),
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }

    public function create(): View
    {
        return $this->formView(new TransportationLocation());
    }

    public function store(Request $request): RedirectResponse
    {
        TransportationLocation::create($this->validatedData($request));

        return redirect()->route('admin.transportation-locations.index')->with('success', 'Data transportasi berhasil ditambahkan.');
    }

    public function edit(TransportationLocation $location): View
    {
        return $this->formView($location);
    }

    public function update(Request $request, TransportationLocation $location): RedirectResponse
    {
        $location->update($this->validatedData($request, $location));

        return redirect()->route('admin.transportation-locations.index')->with('success', 'Data transportasi berhasil diperbarui.');
    }

    public function destroy(TransportationLocation $location): RedirectResponse
    {
        $this->deleteStoredMedia($location->location_media_url);
        $location->delete();

        return back()->with('success', 'Data transportasi berhasil dihapus.');
    }

    private function formView(TransportationLocation $location): View
    {
        return view('pages.admin.transportation-locations.form', [
            'location' => $location,
            'categories' => TransportationCategory::orderBy('category_name')->get(),
            'map' => $this->defaultMap(),
        ]);
    }

    private function validatedData(Request $request, ?TransportationLocation $location = null): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:transportation_categories,id'],
            'location_name' => ['required', 'string', 'max:150'],
            'location_address' => ['nullable', 'string', 'max:255'],
            'location_description' => ['nullable', 'string', 'max:5000'],
            'coordinate_x' => ['required', 'numeric', 'between:0,100'],
            'coordinate_y' => ['required', 'numeric', 'between:0,100'],
            'location_source_media' => ['nullable', 'string', 'max:255'],
            'media' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,mp4', 'max:71680'],
        ]);

        $media = $data['media'] ?? null;
        unset($data['media']);

        if (filled($data['location_description'] ?? null)) {
            $config = new HtmlSanitizerConfig();
            foreach (['p', 'h1', 'h2', 'h3', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li', 'blockquote'] as $tag) {
                $config = $config->allowElement($tag, []);
            }
            $data['location_description'] = str_replace("\u{00A0}", ' ', (new HtmlSanitizer($config))->sanitize($data['location_description']));
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['map_id'] = $this->defaultMap()->id;

        if ($media) {
            $name = Str::slug($data['location_name']) ?: 'transportasi';
            $filename = $name.'-'.Str::uuid().'.'.strtolower($media->extension());
            $this->deleteStoredMedia($location?->location_media_url);
            $data['location_media_url'] = 'storage/'.$media->storeAs('transportation', $filename, 'public');
        }

        return $data;
    }

    private function defaultMap(): TransportationMap
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

    private function deleteStoredMedia(?string $mediaUrl): void
    {
        if ($mediaUrl && str_starts_with($mediaUrl, 'storage/transportation/')) {
            Storage::disk('public')->delete(substr($mediaUrl, 8));
        }
    }
}

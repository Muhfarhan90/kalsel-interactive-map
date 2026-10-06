<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportationCategory;
use App\Models\TourismCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransportationCategoryController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.transportation-categories.index', [
            'categories' => TransportationCategory::withCount('transportation_locations')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.transportation-categories.form', [
            'category' => new TransportationCategory(),
            'iconOptions' => TourismCategory::iconOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        if ($request->hasFile('category_background_image_file')) {
            $data['category_background_image'] = 'storage/'.$request->file('category_background_image_file')->store('transportation/categories', 'public');
        }

        TransportationCategory::create(array_merge($data, [
            'sort_order' => (int) TransportationCategory::max('sort_order') + 1,
        ]));

        return redirect()->route('admin.transportation-categories.index')->with('success', 'Kategori transportasi berhasil ditambahkan.');
    }

    public function edit(TransportationCategory $category): View
    {
        return view('pages.admin.transportation-categories.form', [
            'category' => $category,
            'iconOptions' => TourismCategory::iconOptions(),
        ]);
    }

    public function update(Request $request, TransportationCategory $category): RedirectResponse
    {
        $data = $this->validatedData($request, $category);
        $previousImage = $category->category_background_image;

        if ($request->hasFile('category_background_image_file')) {
            $data['category_background_image'] = 'storage/'.$request->file('category_background_image_file')->store('transportation/categories', 'public');
        } elseif ($request->boolean('remove_background_image')) {
            $data['category_background_image'] = null;
        }

        $category->update($data);
        if ($previousImage !== $category->category_background_image) {
            $this->deleteStoredBackground($previousImage);
        }

        return redirect()->route('admin.transportation-categories.index')->with('success', 'Kategori transportasi berhasil diperbarui.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'distinct'],
        ])['order'];

        $ids = array_map('intval', $order);
        $submittedIds = $ids;
        $existingIds = TransportationCategory::pluck('id')->map(fn ($id) => (int) $id)->all();
        sort($submittedIds);
        sort($existingIds);

        if ($submittedIds !== $existingIds) {
            return back()->with('error', 'Daftar kategori berubah. Muat ulang halaman dan coba lagi.');
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $position => $id) {
                TransportationCategory::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        return back()->with('success', 'Urutan kategori transportasi berhasil diperbarui.');
    }

    public function destroy(TransportationCategory $category): RedirectResponse
    {
        if ($category->transportation_locations()->exists()) {
            return back()->with('error', 'Kategori masih digunakan oleh data transportasi.');
        }

        $backgroundImage = $category->category_background_image;
        $category->delete();
        $this->deleteStoredBackground($backgroundImage);

        return back()->with('success', 'Kategori transportasi berhasil dihapus.');
    }

    private function validatedData(Request $request, ?TransportationCategory $category = null): array
    {
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:100', Rule::unique('transportation_categories')->ignore($category?->id)],
            'category_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'category_icon' => ['required', Rule::in(array_keys(TourismCategory::iconOptions()))],
            'category_description' => ['nullable', 'string', 'max:1000'],
            'category_background_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_background_image' => ['nullable', 'boolean'],
        ]);

        unset($data['category_background_image_file'], $data['remove_background_image']);

        return $data;
    }

    private function deleteStoredBackground(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/transportation/categories/')) {
            Storage::disk('public')->delete(substr($path, 8));
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourismCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TourismCategoryController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.categories.index', [
            'categories' => TourismCategory::withCount('tourism_locations')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.categories.form', [
            'category' => new TourismCategory(),
            'iconOptions' => TourismCategory::iconOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        if ($request->hasFile('category_background_image_file')) {
            $data['category_background_image'] = 'storage/'.$request->file('category_background_image_file')->store('tourism/categories', 'public');
        }

        TourismCategory::create(array_merge($data, [
            'sort_order' => (int) TourismCategory::max('sort_order') + 1,
        ]));

        return redirect()
            ->route('admin.tourism-categories.index')
            ->with('success', 'Kategori wisata berhasil ditambahkan.');
    }

    public function edit(TourismCategory $category): View
    {
        return view('pages.admin.categories.form', [
            'category' => $category,
            'iconOptions' => TourismCategory::iconOptions(),
        ]);
    }

    public function update(Request $request, TourismCategory $category): RedirectResponse
    {
        $data = $this->validatedData($request);
        $previousImage = $category->category_background_image;

        if ($request->hasFile('category_background_image_file')) {
            $data['category_background_image'] = 'storage/'.$request->file('category_background_image_file')->store('tourism/categories', 'public');
        } elseif ($request->boolean('remove_background_image')) {
            $data['category_background_image'] = null;
        }

        $category->update($data);
        if ($previousImage !== $category->category_background_image) {
            $this->deleteStoredBackground($previousImage);
        }

        return redirect()
            ->route('admin.tourism-categories.index')
            ->with('success', 'Kategori wisata berhasil diperbarui.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'distinct'],
        ])['order'];

        $ids = array_map('intval', $order);
        $submittedIds = $ids;
        $existingIds = TourismCategory::pluck('id')->map(fn ($id) => (int) $id)->all();
        sort($submittedIds);
        sort($existingIds);

        if ($submittedIds !== $existingIds) {
            return back()->with('error', 'Daftar kategori berubah. Muat ulang halaman dan coba lagi.');
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $position => $id) {
                TourismCategory::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        return back()->with('success', 'Urutan kategori wisata berhasil diperbarui.');
    }

    public function destroy(TourismCategory $category): RedirectResponse
    {
        if ($category->tourism_locations()->exists()) {
            return back()->with('error', 'Kategori masih digunakan. Hapus atau pindahkan data wisatanya terlebih dahulu.');
        }

        $backgroundImage = $category->category_background_image;
        $category->delete();
        $this->deleteStoredBackground($backgroundImage);

        return back()->with('success', 'Kategori wisata berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:100'],
            'category_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'category_icon' => ['required', Rule::in(array_keys(TourismCategory::iconOptions()))],
            'category_description' => ['required', 'string', 'max:1000'],
            'category_background_image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_background_image' => ['nullable', 'boolean'],
        ]);

        unset($data['category_background_image_file'], $data['remove_background_image']);

        return $data;
    }

    private function deleteStoredBackground(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/tourism/categories/')) {
            Storage::disk('public')->delete(substr($path, 8));
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourismCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        TourismCategory::create(array_merge($this->validatedData($request), [
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
        $category->update($this->validatedData($request));

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

        $category->delete();

        return back()->with('success', 'Kategori wisata berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'category_name' => ['required', 'string', 'max:100'],
            'category_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'category_icon' => ['required', Rule::in(array_keys(TourismCategory::iconOptions()))],
            'category_description' => ['required', 'string', 'max:1000'],
        ]);

        return $data;
    }
}

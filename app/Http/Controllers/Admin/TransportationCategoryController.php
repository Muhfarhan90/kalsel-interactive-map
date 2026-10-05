<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportationCategory;
use App\Models\TourismCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        TransportationCategory::create(array_merge($this->validatedData($request), [
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
        $category->update($this->validatedData($request, $category));

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

        $category->delete();

        return back()->with('success', 'Kategori transportasi berhasil dihapus.');
    }

    private function validatedData(Request $request, ?TransportationCategory $category = null): array
    {
        return $request->validate([
            'category_name' => ['required', 'string', 'max:100', Rule::unique('transportation_categories')->ignore($category?->id)],
            'category_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'category_icon' => ['required', Rule::in(array_keys(TourismCategory::iconOptions()))],
            'category_description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}

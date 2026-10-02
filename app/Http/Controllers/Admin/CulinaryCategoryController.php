<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CulinaryCategory;
use App\Models\TourismCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CulinaryCategoryController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.culinary-categories.index', [
            'categories' => CulinaryCategory::withCount('culinary_locations')->latest()->paginate(10),
        ]);
    }

    public function create(): View
    {
        return view('pages.admin.culinary-categories.form', [
            'category' => new CulinaryCategory(),
            'iconOptions' => TourismCategory::iconOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        CulinaryCategory::create($this->validatedData($request));

        return redirect()->route('admin.culinary-categories.index')->with('success', 'Kategori kuliner berhasil ditambahkan.');
    }

    public function edit(CulinaryCategory $category): View
    {
        return view('pages.admin.culinary-categories.form', [
            'category' => $category,
            'iconOptions' => TourismCategory::iconOptions(),
        ]);
    }

    public function update(Request $request, CulinaryCategory $category): RedirectResponse
    {
        $category->update($this->validatedData($request, $category));

        return redirect()->route('admin.culinary-categories.index')->with('success', 'Kategori kuliner berhasil diperbarui.');
    }

    public function destroy(CulinaryCategory $category): RedirectResponse
    {
        if ($category->culinary_locations()->exists()) {
            return back()->with('error', 'Kategori masih digunakan oleh data kuliner.');
        }

        $category->delete();

        return back()->with('success', 'Kategori kuliner berhasil dihapus.');
    }

    private function validatedData(Request $request, ?CulinaryCategory $category = null): array
    {
        return $request->validate([
            'category_name' => ['required', 'string', 'max:100', Rule::unique('culinary_categories')->ignore($category?->id)],
            'category_color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'category_icon' => ['required', Rule::in(array_keys(TourismCategory::iconOptions()))],
            'category_description' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}

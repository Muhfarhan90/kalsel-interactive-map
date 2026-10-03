<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportationCategory;
use App\Models\TourismCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TransportationCategoryController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.transportation-categories.index', [
            'categories' => TransportationCategory::withCount('transportation_locations')->latest()->paginate(10),
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
        TransportationCategory::create($this->validatedData($request));

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

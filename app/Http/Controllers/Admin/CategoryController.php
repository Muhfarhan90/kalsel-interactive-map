<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $menu = $this->menuFromRequest($request);

        $categories = $menu->categories()
            ->withCount('locations')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('pages.admin.categories.index', [
            'menu' => $menu,
            'categories' => $categories,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $menu = $this->menuFromRequest($request);

        return view('pages.admin.categories.form', [
            'menu' => $menu,
            'category' => new Category(),
            'iconOptions' => Category::iconOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $menu = $this->menuFromRequest($request);
        $data = $this->validatedData($request);
        $data['menu_id'] = $menu->id;

        if ($request->hasFile('background')) {
            $data['background'] = 'storage/' . $request->file('background')->store("menus/categories", 'public');
        }

        Category::create(array_merge($data, [
            'sort_order' => (int) Menu::max('sort_order') + 1,
        ]));

        return redirect()
            ->route('admin.categories.index', ['menu' => $menu->slug])
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        $menu = Menu::findOrFail($category->menu_id);

        return view('pages.admin.categories.form', [
            'menu' => $menu,
            'category' => $category,
            'iconOptions' => Menu::iconOptions(),
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $menu = Menu::findOrFail($category->menu_id);
        $data = $this->validatedData($request);
        $previousBackground = $category->background;

        if ($request->hasFile('background')) {
            $data['background'] = 'storage/' . $request->file('background')->store("menus/categories", 'public');
        } elseif ($request->boolean('remove_background')) {
            $data['background'] = null;
        }

        $category->update($data);

        if ($previousBackground !== $category->background) {
            $this->deleteStoredBackground($previousBackground);
        }

        return redirect()
            ->route('admin.categories.index', ['menu' => $menu->slug])
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $menu = $this->menuFromRequest($request);
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'distinct'],
        ])['order'];

        $ids = array_map('intval', $order);
        $submittedIds = $ids;
        $existingIds = Category::query()
            ->where('menu_id', $menu->id)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        sort($submittedIds);
        sort($existingIds);

        if ($submittedIds !== $existingIds) {
            return back()->with('error', 'Daftar kategori berubah. Muat ulang halaman dan coba lagi.');
        }

        DB::transaction(function () use ($ids, $menu): void {
            foreach ($ids as $position => $id) {
                Category::query()
                    ->where('menu_id', $menu->id)
                    ->whereKey($id)
                    ->update(['sort_order' => $position + 1]);
            }
        });

        return back()->with('success', 'Urutan kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $menu = Menu::findOrFail($category->menu_id);

        if ($category->locations()->exists()) {
            return back()->with('error', 'Kategori masih digunakan oleh lokasi. Pindahkan atau hapus lokasinya terlebih dahulu.');
        }

        $background = $category->background;
        $category->delete();
        $this->deleteStoredBackground($background);

        return redirect()
            ->route('admin.categories.index', ['menu' => $menu->slug])
            ->with('success', 'Kategori berhasil dihapus.');
    }

    private function menuFromRequest(Request $request): Menu
    {
        return Menu::query()->where('slug', $request->query('menu'))->firstOrFail();
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'icon' => ['required', Rule::in(array_keys(Menu::iconOptions()))],
            'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'description' => ['required', 'string', 'max:1000'],
            'background' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_background' => ['nullable', 'boolean'],
        ]);

        unset($data['background'], $data['remove_background']);

        return $data;
    }

    private function deleteStoredBackground(?string $path): void
    {
        if ($path && str_starts_with($path, 'storage/menus/categories/')) {
            Storage::disk('public')->delete(substr($path, strlen('storage/')));
        }
    }
}

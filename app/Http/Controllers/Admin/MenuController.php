<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        $menu = Menu::orderBy('sort_order')->orderBy('id')->get();

        return view('pages.admin.menus.index', compact('menu'));
    }

    public function create(): View
    {
        return view('pages.admin.menus.form', [
            'menu' => new Menu(),
            'iconOptions' => Menu::iconOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        if ($request->hasFile('logo')) {
            $data['logo'] = 'storage/' . $request->file('logo')
                ->store('menus/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            $data['banner'] = 'storage/' . $request->file('banner')
                ->store('menus/banners', 'public');
        }

        Menu::create(array_merge($data, [
            'slug' => $data['slug'],
            'sort_order' => (int) Menu::max('sort_order') + 1,
        ]));

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu): View
    {
        return view('pages.admin.menus.form', [
            'menu' => $menu,
            'iconOptions' => Menu::iconOptions(),
        ]);
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $data = $this->validatedData($request);
        $previousLogo = $menu->logo;
        $previousBanner = $menu->banner;

        if ($data['name'] !== $menu->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $menu);
        }

        if ($request->hasFile('logo')) {
            $data['logo'] = 'storage/' . $request->file('logo')
                ->store("menus/logos", 'public');
        } elseif ($request->boolean('remove_logo')) {
            $data['logo'] = null;
        }

        if ($request->hasFile('banner')) {
            $data['banner'] = 'storage/' . $request->file('banner')
                ->store("menus/banners", 'public');
        } elseif ($request->boolean('remove_banner')) {
            $data['banner'] = null;
        }

        $menu->update($data);

        if ($previousLogo !== $menu->logo) {
            $this->deleteStoredImage($previousLogo, 'menus/logos');
        }

        if ($previousBanner !== $menu->banner) {
            $this->deleteStoredImage($previousBanner, 'menus/banners');
        }

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function reorder(Request $request)
    {
        $order = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'distinct'],
        ])['order'];

        $ids = array_map('intval', $order);
        $submittedIds = $ids;
        $existingIds = Menu::pluck('id')->map(fn($id) => (int) $id)->all();
        sort($submittedIds);
        sort($existingIds);

        if ($submittedIds !== $existingIds) {
            return back()->with('error', 'Daftar menu berubah. Muat ulang halaman dan coba lagi.');
        }

        DB::transaction(function () use ($ids): void {
            foreach ($ids as $position => $id) {
                Menu::whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });

        return back()->with('success', 'Urutan menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $previousLogo = $menu->logo;
        $previousBanner = $menu->banner;

        $menu->delete();

        if ($previousLogo) {
            $this->deleteStoredImage($previousLogo, 'menus/logos');
        }

        if ($previousBanner) {
            $this->deleteStoredImage($previousBanner, 'menus/banners');
        }

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'icon' => ['required', 'string', Rule::in(array_keys(Menu::iconOptions()))],
            'description' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'color' => ['nullable', 'string', 'max:7'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_banner' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        unset($data['logo'], $data['banner'], $data['remove_logo'], $data['remove_banner']);

        return $data;
    }

    private function deleteStoredImage(?string $path, string $directory): void
    {
        $prefix = 'storage/' . trim($directory, '/') . '/';

        if ($path && str_starts_with($path, $prefix)) {
            Storage::disk('public')->delete(substr($path, strlen('storage/')));
        }
    }

    private function uniqueSlug(string $name, ?Menu $except = null): string
    {
        $base = rtrim(substr(Str::slug($name), 0, 240), '-') ?: 'menu';
        $slug = $base;
        $suffix = 2;

        while (true) {
            $query = Menu::query()->where('slug', $slug);

            if ($except !== null) {
                $query->where($except->getKeyName(), '!=', $except->getKey());
            }

            if (! $query->exists()) {
                return $slug;
            }

            $slug = $base . '-' . $suffix++;
        }
    }
}

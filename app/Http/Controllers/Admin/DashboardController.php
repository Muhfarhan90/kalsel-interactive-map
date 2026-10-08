<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\TourismCategory;
use App\Models\TourismLocation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $menus = Menu::query()->with([
            'categories' => fn($query) => $query->withCount(
                ['locations', 'locations as active_locations_count' => fn($query) => $query->where('is_active', true)]
            )
        ])->orderBy('sort_order')->orderBy('id')->get();

        $categories = $menus->flatMap(fn(Menu $menu) => $menu->categories);

        return view('pages.admin.dashboard', [
            'menus' => $menus,
            'menuCount' => $menus->count(),
            'activeMenuCount' => $menus->where('is_active', true)->count(),
            'categoryCount' => $categories->count(),
            'locationCount' => $categories->sum('locations_count'),
            'activeLocationCount' => $categories->sum('active_locations_count'),
        ]);
    }
}

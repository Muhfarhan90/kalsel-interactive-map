<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourismCategory;
use App\Models\TourismLocation;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.admin.dashboard', [
            'categoryCount' => TourismCategory::count(),
            'locationCount' => TourismLocation::count(),
            'activeLocationCount' => TourismLocation::where('is_active', true)->count(),
        ]);
    }
}

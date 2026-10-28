<?php

use App\Http\Controllers\Admin\AdminHomepageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CulinaryCategoryController;
use App\Http\Controllers\Admin\CulinaryLocationController;
use App\Http\Controllers\Admin\CulinaryMapController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PublicPageHeaderController;
use App\Http\Controllers\Admin\RegencyController;
use App\Http\Controllers\Admin\TourismCategoryController;
use App\Http\Controllers\Admin\TourismLocationController;
use App\Http\Controllers\Admin\TourismMapController;
use App\Http\Controllers\Admin\TransportationCategoryController;
use App\Http\Controllers\Admin\TransportationLocationController;
use App\Http\Controllers\Admin\TransportationMapController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomepageController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store']);
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('headers', [PublicPageHeaderController::class, 'edit'])->name('headers.edit');
    Route::put('headers', [PublicPageHeaderController::class, 'update'])->name('headers.update');
    Route::get('homepage', [AdminHomepageController::class, 'edit'])->name('homepage.edit');
    Route::put('homepage', [AdminHomepageController::class, 'update'])->name('homepage.update');
    Route::get('maps', [MapController::class, 'edit'])->name('maps.edit');
    Route::put('maps', [MapController::class, 'update'])->name('maps.update');
    Route::post('regencies/reorder', [RegencyController::class, 'reorder'])->name('regencies.reorder');
    Route::resource('regencies', RegencyController::class)->except('show');

    Route::resource('menus', MenuController::class)->except('show');
    Route::post('menus/reorder', [MenuController::class, 'reorder'])->name('menus.reorder');
    Route::resource('categories', CategoryController::class)->except('show');
    Route::post('categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
    Route::post('locations/{location}/gallery/reorder', [
        LocationController::class, 'reorderGallery',
    ])->name('locations.gallery.reorder');
    Route::resource('locations', LocationController::class)->except('show');
    Route::resource('users', UserController::class)->except('show');
});

Route::redirect('/wilayah/{regency}/{menu}', '/{menu}/{regency}', 301);
Route::get('/destinasi-pariwisata', [
    HomepageController::class, 'allLocations'
])->name('map.all');
Route::get('/{menu:slug}/{regency:slug}', [HomepageController::class, 'regencyMenu'])
    // Wilayah dipakai bersama semua menu, jadi binding wilayah tidak dibatasi oleh relasi menu.
    ->withoutScopedBindings()
    ->name('regencies.menu');
Route::get('/{slug}', [HomepageController::class, 'menu'])->name('menu');

Route::get('/', [HomepageController::class, 'index'])->name('home');

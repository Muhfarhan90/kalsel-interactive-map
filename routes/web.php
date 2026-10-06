<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CulinaryCategoryController;
use App\Http\Controllers\Admin\CulinaryLocationController;
use App\Http\Controllers\Admin\CulinaryMapController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\PublicPageHeaderController;
use App\Http\Controllers\Admin\TransportationCategoryController;
use App\Http\Controllers\Admin\TransportationLocationController;
use App\Http\Controllers\Admin\TransportationMapController;
use App\Http\Controllers\Admin\TourismCategoryController;
use App\Http\Controllers\Admin\TourismLocationController;
use App\Http\Controllers\Admin\TourismMapController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CulinaryController;
use App\Http\Controllers\TransportationController;
use App\Http\Controllers\TourismController;
use App\Models\CulinaryMap;
use App\Models\PublicPageHeader;
use App\Models\TourismMap;
use App\Models\TransportationMap;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('pages.home', [
    'pageHeader' => PublicPageHeader::where('page_key', 'home')->firstOrFail(),
    'tourismMap' => TourismMap::latest('id')->first(),
    'culinaryMap' => CulinaryMap::latest('id')->first(),
    'transportationMap' => TransportationMap::latest('id')->first(),
]))->name('home');
Route::get('/wisata', TourismController::class)->name('tourism');
Route::get('/kuliner', CulinaryController::class)->name('culinary');
Route::get('/transportasi', TransportationController::class)->name('transportation');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store']);
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('headers', [PublicPageHeaderController::class, 'edit'])->name('headers.edit');
    Route::put('headers', [PublicPageHeaderController::class, 'update'])->name('headers.update');
    Route::get('maps', [MapController::class, 'edit'])->name('maps.edit');
    Route::put('maps', [MapController::class, 'update'])->name('maps.update');
    Route::get('tourism-map', [TourismMapController::class, 'edit'])->name('tourism-map.edit');
    Route::put('tourism-map', [TourismMapController::class, 'update'])->name('tourism-map.update');
    Route::post('tourism-categories/reorder', [TourismCategoryController::class, 'reorder'])->name('tourism-categories.reorder');
    Route::resource('tourism-categories', TourismCategoryController::class)
        ->except('show')
        ->parameters(['tourism-categories' => 'category']);
    Route::get('culinary-map', [CulinaryMapController::class, 'edit'])->name('culinary-map.edit');
    Route::put('culinary-map', [CulinaryMapController::class, 'update'])->name('culinary-map.update');
    Route::post('culinary-categories/reorder', [CulinaryCategoryController::class, 'reorder'])->name('culinary-categories.reorder');
    Route::resource('culinary-categories', CulinaryCategoryController::class)
        ->except('show')
        ->parameters(['culinary-categories' => 'category']);
    Route::resource('culinary-locations', CulinaryLocationController::class)
        ->except('show')
        ->parameters(['culinary-locations' => 'location']);
    Route::get('transportation-map', [TransportationMapController::class, 'edit'])->name('transportation-map.edit');
    Route::put('transportation-map', [TransportationMapController::class, 'update'])->name('transportation-map.update');
    Route::post('transportation-categories/reorder', [TransportationCategoryController::class, 'reorder'])->name('transportation-categories.reorder');
    Route::resource('transportation-categories', TransportationCategoryController::class)
        ->except('show')
        ->parameters(['transportation-categories' => 'category']);
    Route::resource('transportation-locations', TransportationLocationController::class)
        ->except('show')
        ->parameters(['transportation-locations' => 'location']);
    Route::delete('locations/{location}/media', [TourismLocationController::class, 'destroyMedia'])->name('locations.media.destroy');
    Route::resource('locations', TourismLocationController::class)->except('show');
    Route::resource('users', UserController::class)->except('show');
});

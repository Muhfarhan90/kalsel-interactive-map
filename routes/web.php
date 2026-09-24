<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourismCategoryController;
use App\Http\Controllers\Admin\TourismLocationController;
use App\Http\Controllers\TourismController;
use Illuminate\Support\Facades\Route;

Route::get('/', TourismController::class)->name('tourism');
Route::get('/admin', DashboardController::class)->name('dashboard');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::delete('locations/{location}/media', [TourismLocationController::class, 'destroyMedia'])->name('locations.media.destroy');
    Route::resource('categories', TourismCategoryController::class)->except('show');
    Route::delete('locations/{location}/media', [TourismLocationController::class, 'destroyMedia'])->name('locations.media.destroy');
    Route::resource('locations', TourismLocationController::class)->except('show');
});

<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourismCategoryController;
use App\Http\Controllers\Admin\TourismLocationController;
use App\Http\Controllers\Admin\TourismMapController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TourismController;
use Illuminate\Support\Facades\Route;

Route::get('/', TourismController::class)->name('tourism');
Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store']);
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('map', [TourismMapController::class, 'edit'])->name('map.edit');
    Route::put('map', [TourismMapController::class, 'update'])->name('map.update');
    Route::resource('categories', TourismCategoryController::class)->except('show');
    Route::delete('locations/{location}/media', [TourismLocationController::class, 'destroyMedia'])->name('locations.media.destroy');
    Route::resource('locations', TourismLocationController::class)->except('show');
    Route::resource('users', UserController::class)->except('show');
});

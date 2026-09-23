<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocaleController;

// Locale Switch Route
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');
use App\Http\Controllers\DashboardController;

// dashboard pages
Route::get('/', function () {
    return view('pages.tourism');
})->name('tourism');

Route::get('/admin', function () {
    return view('pages.admin.dashboard');
})->name('dashboard');

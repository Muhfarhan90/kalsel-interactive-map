<?php

use Illuminate\Support\Facades\Route;

// dashboard pages
Route::get('/', function () {
    return view('pages.tourism');
})->name('tourism');

Route::get('/admin', function () {
    return view('pages.admin.dashboard');
})->name('dashboard');

<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ComponentController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    
    Route::resource('companies',CompanyController::class);

    Route::resource('categories', CategoryController::class);

    Route::resource('components', ComponentController::class);

});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';

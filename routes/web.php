<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('animals', AnimalController::class);
    Route::resource('inventory', InventoryController::class);
});

require __DIR__.'/auth.php';

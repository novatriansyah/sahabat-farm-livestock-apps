<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\AnimalPrintController;
use App\Http\Controllers\BreedingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExitController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryPurchaseController;
use App\Http\Controllers\InventoryUsageController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\OperatorInventoryController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {

    // Manager Routes (Owner Only)
    Route::middleware(['role:OWNER'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Master Data
        Route::get('masters', [MasterDataController::class, 'index'])->name('masters.index');
        Route::post('masters/breed', [MasterDataController::class, 'storeBreed'])->name('masters.breed.store');
        Route::post('masters/location', [MasterDataController::class, 'storeLocation'])->name('masters.location.store');
        Route::post('masters/disease', [MasterDataController::class, 'storeDisease'])->name('masters.disease.store');

        // User Management
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'destroy']);

        // Animal Management (Create/Edit/Exit)
        Route::resource('animals', AnimalController::class);
        Route::get('animals/{animal}/print', [AnimalPrintController::class, 'show'])->name('animals.print');

        // Breeding Flow
        Route::get('animals/{animal}/breeding/create', [BreedingController::class, 'create'])->name('breeding.create');
        Route::post('animals/{animal}/breeding', [BreedingController::class, 'store'])->name('breeding.store');

        // Exit Flow
        Route::get('animals/{animal}/exit', [ExitController::class, 'create'])->name('animals.exit.create');
        Route::post('animals/{animal}/exit', [ExitController::class, 'store'])->name('animals.exit.store');

        // Inventory Management (Full Access)
        Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/purchase', [InventoryPurchaseController::class, 'store'])->name('inventory.purchase.store');
    });

    // Operator Routes (Staff & Owner)
    // Note: RoleMiddleware for 'STAFF' allows 'OWNER' access too.
    Route::middleware(['role:STAFF'])->group(function () {
        Route::get('scan', [ScanController::class, 'index'])->name('scan.index');

        // Operator Workflow
        Route::get('operator/inventory', [OperatorInventoryController::class, 'index'])->name('operator.inventory.index');
        Route::post('inventory/usage', [InventoryUsageController::class, 'store'])->name('inventory.usage.store'); // Moved here so Staff can access

        Route::get('operator/{animal}', [OperatorController::class, 'show'])->name('operator.show');
        Route::post('operator/{animal}/weight', [OperatorController::class, 'storeWeight'])->name('operator.weight.store');
        Route::post('operator/{animal}/health', [OperatorController::class, 'storeHealth'])->name('operator.health.store');
        Route::post('operator/{animal}/move', [OperatorController::class, 'moveCage'])->name('operator.cage.move');
    });
});

require __DIR__.'/auth.php';

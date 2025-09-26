<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Owner\BillController;
use App\Http\Controllers\Owner\FlatController;
use App\Http\Controllers\Owner\PaymentController;
use App\Http\Controllers\Owner\AssignFlatController;
use App\Http\Controllers\Owner\BillCategoryController;
use App\Http\Controllers\Owner\TenantOccupancyController;
use App\Http\Controllers\Admin\OwnerController as AdminOwnerController;
use App\Http\Controllers\Admin\TenantController as AdminTenantController;
use App\Http\Controllers\Admin\BuildingController as AdminBuildingController;
use App\Http\Controllers\Owner\BuildingController as OwnerBuildingController;
use App\Http\Controllers\Admin\BuildingTenantController as AdminBuildingTenantController;
use App\Http\Controllers\Owner\BuildingTenantController as OwnerBuildingTenantController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Owners
    Route::resource('owners', AdminOwnerController::class);

    // Admin Tenants
    Route::resource('tenants', AdminTenantController::class);

    // Admin Buildings
    Route::resource('buildings', AdminBuildingController::class);

    // Admin Building Tenants
    Route::prefix('buildings/{building}')->name('buildings.')->group(function () {
        Route::resource('tenants', AdminBuildingTenantController::class)->except(['show', 'edit', 'update']);
    });
});

// Owner routes
Route::middleware(['auth', 'can:owner'])->prefix('owner')->name('owner.')->group(function () {
    // Owner Buildings
    Route::get('buildings', [OwnerBuildingController::class, 'index'])->name('buildings.index');
    Route::get('buildings/{building}', [OwnerBuildingController::class, 'show'])->name('buildings.show');
    Route::get('buildings/search', [OwnerBuildingController::class, 'search'])->name('buildings.search');

    // Owner Building Tenants
    Route::get('buildings/{building}/tenants', [OwnerBuildingTenantController::class, 'index'])->name('buildings.tenants.index');

    // Flats by building
    Route::get('buildings/{building}/flats', [FlatController::class, 'indexByBuilding'])->name('buildings.flats.index');
    Route::get('buildings/{building}/flats/create', [FlatController::class, 'createForBuilding'])->name('buildings.flats.create');
    Route::post('buildings/{building}/flats', [FlatController::class, 'storeForBuilding'])->name('buildings.flats.store');

    // Flat management
    Route::get('flats/{flat}/edit', [FlatController::class, 'edit'])->name('flats.edit');
    Route::put('flats/{flat}', [FlatController::class, 'update'])->name('flats.update');
    Route::delete('flats/{flat}', [FlatController::class, 'destroy'])->name('flats.destroy');

    // Tenant occupancy management
    Route::get('buildings/{building}/tenants/{tenant}/occupancies', [TenantOccupancyController::class, 'index'])
        ->name('buildings.tenants.occupancies.index');
    Route::get('buildings/{building}/tenants/{tenant}/occupancies/create', [TenantOccupancyController::class, 'create'])
        ->name('buildings.tenants.occupancies.create');
    Route::post('buildings/{building}/tenants/{tenant}/occupancies', [TenantOccupancyController::class, 'store'])
        ->name('buildings.tenants.occupancies.store');
    Route::get('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}/edit', [TenantOccupancyController::class, 'edit'])
        ->name('buildings.tenants.occupancies.edit');
    Route::put('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}', [TenantOccupancyController::class, 'update'])
        ->name('buildings.tenants.occupancies.update');
    Route::put('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}/end', [TenantOccupancyController::class, 'end'])
        ->name('buildings.tenants.occupancies.end');
    Route::delete('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}', [TenantOccupancyController::class, 'destroy'])
        ->name('buildings.tenants.occupancies.destroy');

    // Bill categories and bills
    Route::resource('categories', BillCategoryController::class)->except(['show']);
    Route::resource('bills', BillController::class)->only(['index', 'create', 'store']);

    // show create payment form for a bill
    Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create'); // ?bill_id=123

    // store payment
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');

    // optional: list and delete payments
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index'); // filterable list
    Route::delete('payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
});

require __DIR__.'/auth.php';
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Owner\BillController;
use App\Http\Controllers\Owner\FlatController;
use App\Http\Controllers\Owner\PaymentController;
use App\Http\Controllers\Owner\AdjustmentController;
use App\Http\Controllers\Owner\AssignFlatController;
use App\Http\Controllers\Owner\BillCategoryController;
use App\Http\Controllers\Owner\TenantOccupancyController;
use App\Http\Controllers\Admin\OwnerController as AdminOwner;
use App\Http\Controllers\Admin\TenantController as AdminTenant;
use App\Http\Controllers\Admin\BuildingController as AdminBuilding;
use App\Http\Controllers\Owner\BuildingController as OwnerBuilding;
use App\Http\Controllers\Admin\BuildingTenantController as AdminBuildingTenant;
use App\Http\Controllers\Owner\BuildingTenantController as OwnerBuildingTenant;

Route::get('/', function () {
     return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','can:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {
        Route::get('/owners',        [AdminOwner::class, 'index'])->name('owners.index');
        Route::get('/owners/create', [AdminOwner::class, 'create'])->name('owners.create');
        Route::post('/owners',       [AdminOwner::class, 'store'])->name('owners.store');
        Route::get('/owners/{owner}/edit', [AdminOwner::class, 'edit'])->name('owners.edit');
        Route::put('/owners/{owner}',       [AdminOwner::class, 'update'])->name('owners.update');
        Route::delete('/owners/{owner}',    [AdminOwner::class, 'destroy'])->name('owners.destroy');

        Route::get('/tenants',        [AdminTenant::class, 'index'])->name('tenants.index');
        Route::get('/tenants/create', [AdminTenant::class, 'create'])->name('tenants.create');
        Route::post('/tenants',       [AdminTenant::class, 'store'])->name('tenants.store');
        Route::get('/tenants/{tenant}/edit', [AdminTenant::class, 'edit'])->name('tenants.edit');
        Route::put('/tenants/{tenant}',       [AdminTenant::class, 'update'])->name('tenants.update');
        Route::delete('/tenants/{tenant}',    [AdminTenant::class, 'destroy'])->name('tenants.destroy');
        Route::get('/tenants/{tenant}', [AdminTenant::class, 'show'])->name('tenants.show');

        Route::get('buildings/{building}/tenants',          [AdminBuildingTenant::class, 'index'])->name('buildings.tenants.index');
        Route::get('buildings/{building}/tenants/create',   [AdminBuildingTenant::class, 'create'])->name('buildings.tenants.create');
        Route::post('buildings/{building}/tenants',         [AdminBuildingTenant::class, 'store'])->name('buildings.tenants.store');
        Route::delete('buildings/{building}/tenants/{tenant}', [AdminBuildingTenant::class, 'destroy'])->name('buildings.tenants.destroy');

        Route::resource('buildings', AdminBuilding::class); // index, create, store, show, edit, update, destroy

    });


Route::middleware(['auth','can:owner'])
    ->prefix('owner')->name('owner.')
    ->group(function () {

        // List buildings for the owner
        Route::get('buildings', [OwnerBuilding::class,'index'])->name('buildings.index');

        // List tenants for a specific building (owner scope)
        Route::get('buildings/{building}/tenants', [OwnerBuildingTenant::class, 'index'])->name('buildings.tenants.index');

        // flats by building
        Route::resource('buildings.flats', FlatController::class)
            ->only(['index', 'create', 'store']);

        // optional edit/update/delete (still scope by owner)
        Route::resource('flats', FlatController::class)
            ->only(['edit', 'update', 'destroy']);

        // Bill categories management (CRUD except show)
        Route::resource('categories', BillCategoryController::class)->except(['show']);

        // Tenant-wise flat assignments inside a building
        Route::resource('buildings.tenants.occupancies', TenantOccupancyController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);

        // End (set end_date = today or chosen date)
        Route::put('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}/end', [TenantOccupancyController::class,'end'])
            ->name('buildings.tenants.occupancies.end');

        // Delete an incorrect record (rare, for mistakes)
        Route::delete('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}', [TenantOccupancyController::class,'destroy'])
            ->name('buildings.tenants.occupancies.destroy');

        // Bill CRUD routes for owners (except 'show')
        Route::resource('bills', BillController::class)
            ->only(['index','create','store','edit','update','destroy']);

        // Show payments for a specific bill
        Route::get('bills/{bill}/payments', [BillController::class, 'payments'])->name('bills.payments');

        // Payments: allow create, store, and destroy only
        Route::resource('payments', PaymentController::class)
            ->only(['create', 'store', 'destroy']);

        // Adjustments: allow create and store only
        Route::resource('adjustments', AdjustmentController::class)
            ->only(['create', 'store']);
});

require __DIR__.'/auth.php';
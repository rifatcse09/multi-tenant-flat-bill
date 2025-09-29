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

        Route::get('buildings/{building}/tenants',          [AdminBuildingTenant::class, 'index'])->name('buildings.tenants.index');
        Route::get('buildings/{building}/tenants/create',   [AdminBuildingTenant::class, 'create'])->name('buildings.tenants.create');
        Route::post('buildings/{building}/tenants',         [AdminBuildingTenant::class, 'store'])->name('buildings.tenants.store');
        Route::delete('buildings/{building}/tenants/{tenant}', [AdminBuildingTenant::class, 'destroy'])->name('buildings.tenants.destroy');

        Route::resource('buildings', AdminBuilding::class); // index, create, store, show, edit, update, destroy

    });


Route::middleware(['auth','can:owner'])
    ->prefix('owner')->name('owner.')
    ->group(function () {
        Route::get('buildings', [OwnerBuilding::class,'index'])->name('buildings.index');
        Route::get('buildings/{building}/tenants', [OwnerBuildingTenant::class, 'index'])->name('buildings.tenants.index');

        // 2) assign a building-approved tenant to a flat inside that building
        Route::get('buildings/{building}/tenants/{tenant}/assign', [AssignFlatController::class, 'create'])
        ->name('buildings.tenants.assign.create');
        Route::post('buildings/{building}/tenants/{tenant}/assign', [AssignFlatController::class, 'store'])
        ->name('buildings.tenants.assign.store');

        // flats by building
        Route::get('buildings/{building}/flats', [FlatController::class,'indexByBuilding'])->name('buildings.flats.index');
        Route::get('buildings/{building}/flats/create', [FlatController::class,'createForBuilding'])->name('buildings.flats.create');
        Route::post('buildings/{building}/flats', [FlatController::class,'storeForBuilding'])->name('buildings.flats.store');

        // optional edit/update/delete (still scope by owner)
        Route::get('flats/{flat}/edit', [FlatController::class,'edit'])->name('flats.edit');
        Route::put('flats/{flat}', [FlatController::class,'update'])->name('flats.update');
        Route::delete('flats/{flat}', [FlatController::class,'destroy'])->name('flats.destroy');

        Route::resource('categories', BillCategoryController::class)->except(['show']);


        // Tenant-wise flat assignments inside a building
        Route::get('buildings/{building}/tenants/{tenant}/occupancies', [TenantOccupancyController::class,'index'])
            ->name('buildings.tenants.occupancies.index');

        Route::get('buildings/{building}/tenants/{tenant}/occupancies/create', [TenantOccupancyController::class,'create'])
            ->name('buildings.tenants.occupancies.create');
        Route::post('buildings/{building}/tenants/{tenant}/occupancies', [TenantOccupancyController::class,'store'])
            ->name('buildings.tenants.occupancies.store');

        Route::get('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}/edit', [TenantOccupancyController::class,'edit'])
            ->name('buildings.tenants.occupancies.edit');
        Route::put('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}', [TenantOccupancyController::class,'update'])
            ->name('buildings.tenants.occupancies.update');

        // End (set end_date = today or chosen date)
        Route::put('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}/end', [TenantOccupancyController::class,'end'])
            ->name('buildings.tenants.occupancies.end');

        // Delete an incorrect record (rare, for mistakes)
        Route::delete('buildings/{building}/tenants/{tenant}/occupancies/{pivotId}', [TenantOccupancyController::class,'destroy'])
            ->name('buildings.tenants.occupancies.destroy');

         Route::resource('bills', BillController::class)
            ->only(['index','create','store']);

        Route::get('bills/{bill}/edit', [BillController::class, 'edit'])->name('bills.edit');
        Route::put('bills/{bill}', [BillController::class, 'update'])->name('bills.update');
        Route::delete('bills/{bill}', [BillController::class, 'destroy'])->name('bills.destroy');
        Route::get('bills/{bill}/payments', [BillController::class, 'payments'])->name('bills.payments');

        Route::get('payments/create', [PaymentController::class,'create'])->name('payments.create');
        Route::post('payments',        [PaymentController::class,'store'])->name('payments.store');

        Route::get('adjustments/create', [AdjustmentController::class,'create'])->name('adjustments.create');
        Route::post('adjustments',       [AdjustmentController::class,'store'])->name('adjustments.store');
});





require __DIR__.'/auth.php';
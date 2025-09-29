<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Owner\BillController;
use App\Http\Controllers\Owner\FlatController;
use App\Http\Controllers\Owner\PaymentController;
use App\Http\Controllers\Owner\AdjustmentController;
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
    Route::resource('owners', ProfileController::class)
    ->only(['edit', 'update', 'destroy']);
});

Route::middleware(['auth','can:admin'])
    ->prefix('admin')->name('admin.')
    ->group(function () {

        // Owners management (admin scope)
        Route::resource('owners', AdminOwner::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        // Registers resource routes for 'tenants' using the AdminTenant controller, limited to index, create, store, edit, update, show, and destroy actions.
        Route::resource('tenants', AdminTenant::class)
        ->only(['index', 'create', 'store', 'edit', 'update', 'show', 'destroy']);

        // Tenants management for a specific building (admin scope)
        Route::resource('buildings.tenants', AdminBuildingTenant::class)
        ->only(['index', 'create', 'store', 'destroy']);

        // Buildings management (admin scope)
        Route::resource('buildings', AdminBuilding::class);

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
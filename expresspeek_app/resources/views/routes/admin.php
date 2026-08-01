<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\RateImportController;
use App\Http\Controllers\Admin\ShipmentController as AdminShipmentController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SourcingRequestController as AdminSourcingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        Route::resource('users', AdminUserController::class);

        // Monthly rate uploads
        Route::get('/rates/import', [RateImportController::class, 'create'])->name('rates.import.create');
        Route::post('/rates/import', [RateImportController::class, 'store'])->name('rates.import.store');

        // Shipments management
        Route::get('/shipments', [AdminShipmentController::class, 'index'])->name('shipments.index');
        Route::get('/shipments/{shipment}/edit', [AdminShipmentController::class, 'edit'])->name('shipments.edit');
        Route::put('/shipments/{shipment}', [AdminShipmentController::class, 'update'])->name('shipments.update');
        Route::get('/shipments/{shipment}/waybill', [AdminShipmentController::class, 'printWaybill'])->name('shipments.waybill');
        Route::get('/shipments/{shipment}/invoice', [AdminShipmentController::class, 'printInvoice'])->name('shipments.invoice');

        // Sourcing Requests management
        Route::get('/sourcing-requests', [AdminSourcingController::class, 'index'])->name('sourcing-requests.index');
        Route::get('/sourcing-requests/{sourcingRequest}', [AdminSourcingController::class, 'show'])->name('sourcing-requests.show');
        Route::put('/sourcing-requests/{sourcingRequest}', [AdminSourcingController::class, 'update'])->name('sourcing-requests.update');
        Route::delete('/sourcing-requests/{sourcingRequest}', [AdminSourcingController::class, 'destroy'])->name('sourcing-requests.destroy');
    });

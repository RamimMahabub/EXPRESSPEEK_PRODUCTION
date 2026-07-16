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
        Route::get('/dashboard/analytics', [AdminDashboard::class, 'analyticsData'])->name('dashboard.analytics');

        Route::resource('users', AdminUserController::class);

        // Monthly rate uploads
        Route::get('/rates/import', [RateImportController::class, 'create'])->name('rates.import.create');
        Route::post('/rates/import', [RateImportController::class, 'store'])->name('rates.import.store');

        // Address Book
        Route::get('/address-book/manage', [AdminShipmentController::class, 'addressBookPage'])->name('address-book.page');
        Route::get('/address-book', [AdminShipmentController::class, 'getAddressBook'])->name('address-book.index');
        Route::post('/address-book', [AdminShipmentController::class, 'saveAddress'])->name('address-book.store');
        Route::delete('/address-book/{addressBook}', [AdminShipmentController::class, 'deleteAddress'])->name('address-book.destroy');

        // Shipments management
        Route::get('/shipments', [AdminShipmentController::class, 'index'])->name('shipments.index');
        Route::get('/shipments/create', [AdminShipmentController::class, 'create'])->name('shipments.create');
        Route::post('/shipments', [AdminShipmentController::class, 'store'])->name('shipments.store');
        Route::get('/carriers', [AdminShipmentController::class, 'getCarriers'])->name('carriers.available');
        Route::get('/shipments/{shipment}/edit', [AdminShipmentController::class, 'edit'])->name('shipments.edit');
        Route::put('/shipments/{shipment}', [AdminShipmentController::class, 'update'])->name('shipments.update');
        Route::patch('/shipments/{shipment}/inline', [AdminShipmentController::class, 'updateInline'])->name('shipments.update-inline');
        Route::delete('/shipments/{shipment}', [AdminShipmentController::class, 'destroy'])->name('shipments.destroy');
        Route::get('/shipments/{shipment}/waybill', [AdminShipmentController::class, 'printWaybill'])->name('shipments.waybill');
        Route::get('/shipments/{shipment}/invoice', [AdminShipmentController::class, 'printInvoice'])->name('shipments.invoice');

        // Sourcing Requests management
        Route::get('/sourcing-requests', [AdminSourcingController::class, 'index'])->name('sourcing-requests.index');
        Route::get('/sourcing-requests/{sourcingRequest}', [AdminSourcingController::class, 'show'])->name('sourcing-requests.show');
        Route::put('/sourcing-requests/{sourcingRequest}', [AdminSourcingController::class, 'update'])->name('sourcing-requests.update');
        Route::patch('/sourcing-requests/{sourcingRequest}/inline', [AdminSourcingController::class, 'updateInline'])->name('sourcing-requests.update-inline');
        Route::delete('/sourcing-requests/{sourcingRequest}', [AdminSourcingController::class, 'destroy'])->name('sourcing-requests.destroy');

        // Sourcing Invoices
        Route::get('/sourcing-requests/{sourcingRequest}/invoice', [\App\Http\Controllers\Admin\SourcingInvoiceController::class, 'create'])->name('sourcing-requests.invoice.create');
        Route::post('/sourcing-requests/{sourcingRequest}/invoice', [\App\Http\Controllers\Admin\SourcingInvoiceController::class, 'store'])->name('sourcing-requests.invoice.store');
        Route::patch('/sourcing-invoices/{sourcingInvoice}/pay', [\App\Http\Controllers\Admin\SourcingInvoiceController::class, 'markAsPaid'])->name('sourcing-invoices.pay');
        Route::delete('/sourcing-invoices/{sourcingInvoice}', [\App\Http\Controllers\Admin\SourcingInvoiceController::class, 'destroy'])->name('sourcing-invoices.destroy');
        Route::get('/sourcing-invoices/{sourcingInvoice}/download', [\App\Http\Controllers\Admin\SourcingInvoiceController::class, 'downloadPdf'])->name('sourcing-invoices.download');
    });

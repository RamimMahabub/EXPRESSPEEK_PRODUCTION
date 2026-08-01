<?php

use App\Http\Controllers\Agent\DashboardController as AgentDashboard;
use App\Http\Controllers\Agent\ShipmentController as AgentShipment;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])
    ->prefix('agent')
    ->name('agent.')
    ->group(function () {
        // Create Shipment Wizard
        Route::get('/shipments/create', [AgentShipment::class, 'create'])->name('shipments.create');
        Route::post('/shipments', [AgentShipment::class, 'store'])->name('shipments.store');

        // Address Book page
        Route::get('/address-book/manage', [AgentShipment::class, 'addressBookPage'])->name('address-book.page');

        // Carriers for destination country
        Route::get('/carriers', [AgentShipment::class, 'getCarriers'])->name('carriers.available');

        // Address Book
        Route::get('/address-book', [AgentShipment::class, 'getAddressBook'])->name('address-book.index');
        Route::post('/address-book', [AgentShipment::class, 'saveAddress'])->name('address-book.store');
        Route::delete('/address-book/{addressBook}', [AgentShipment::class, 'deleteAddress'])->name('address-book.destroy');
    });

// Note: waybill printing is admin-only. We do not forward or expose agent waybill
// routes to non-admins. Invoice printing remains available to agents.

Route::middleware(['auth', 'verified', 'role:agent'])
    ->prefix('agent')
    ->name('agent.')
    ->group(function () {
        Route::get('/dashboard', [AgentDashboard::class, 'index'])->name('dashboard');

        // Agent shipment queue
        Route::get('/shipments', [AgentShipment::class, 'index'])->name('shipments.index');
        Route::get('/shipments/{shipment}/edit', [AgentShipment::class, 'edit'])->name('shipments.edit');
        Route::put('/shipments/{shipment}', [AgentShipment::class, 'update'])->name('shipments.update');

        // PDF documents: agents may print invoices only; waybills are admin-only.
        Route::get('/shipments/{shipment}/invoice', [AgentShipment::class, 'printInvoice'])->name('shipments.invoice');
    });

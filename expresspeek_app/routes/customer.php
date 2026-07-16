<?php

use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');
        Route::get('/track', [CustomerDashboard::class, 'track'])->name('track');

        // Address Book
        Route::get('/address-book', [\App\Http\Controllers\Customer\ShipmentController::class, 'addressBookPage'])->name('address-book.page');
        Route::get('/api/address-book', [\App\Http\Controllers\Customer\ShipmentController::class, 'getAddressBook'])->name('address-book.api.index');
        Route::post('/api/address-book', [\App\Http\Controllers\Customer\ShipmentController::class, 'saveAddress'])->name('address-book.api.store');
        Route::delete('/api/address-book/{addressBook}', [\App\Http\Controllers\Customer\ShipmentController::class, 'deleteAddress'])->name('address-book.api.destroy');

        // Shipments
        Route::get('/shipments', function () {
            $shipments = auth()->user()->shipments()->with('trackingEvents')->latest()->paginate(15);
            return view('customer.shipments.index', compact('shipments'));
        })->name('shipments.index');

        // Sourcing Requests
        Route::get('/sourcing-requests', function () {
            $sourcingRequests = \App\Models\SourcingRequest::with(['items', 'invoices'])->where('user_id', auth()->id())->latest()->paginate(15);
            return view('customer.sourcing-requests.index', compact('sourcingRequests'));
        })->name('sourcing-requests.index');

        Route::get('/shipments/create', [\App\Http\Controllers\Customer\ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('/shipments', [\App\Http\Controllers\Customer\ShipmentController::class, 'store'])->name('shipments.store');
        Route::get('/api/carriers', [\App\Http\Controllers\Customer\ShipmentController::class, 'getCarriers'])->name('carriers.api.index');

        Route::get('/shipments/{shipment}', function (\App\Models\Shipment $shipment) {
            abort_if($shipment->sender_id !== auth()->id(), 403);
            return view('customer.shipments.show', compact('shipment'));
        })->name('shipments.show');

        // Allow customers to print their own documents
        Route::get('/shipments/{shipment}/waybill', [\App\Http\Controllers\Customer\ShipmentPrintController::class, 'printWaybill'])->name('shipments.waybill');
        Route::get('/shipments/{shipment}/invoice', [\App\Http\Controllers\Customer\ShipmentPrintController::class, 'printInvoice'])->name('shipments.invoice');

        // Sourcing Invoices
        Route::get('/sourcing-invoices/{sourcingInvoice}/download', [\App\Http\Controllers\Customer\SourcingInvoiceController::class, 'downloadPdf'])->name('sourcing-invoices.download');
    });

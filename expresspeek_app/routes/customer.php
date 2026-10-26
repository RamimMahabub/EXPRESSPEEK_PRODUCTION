<?php

use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

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
            $allowedStatuses = array_keys(\App\Models\Shipment::statuses());
            $statuses = array_values(array_intersect((array) request()->query('statuses', []), $allowedStatuses));
            $query = auth()->user()->shipments()->with('trackingEvents')->latest();

            if ($statuses !== []) {
                $query->whereIn('status', $statuses);
            }

            $shipments = $query->paginate(15)->withQueryString();
            return view('customer.shipments.index', compact('shipments'));
        })->name('shipments.index');

        // Sourcing Requests
        Route::get('/sourcing-requests', [\App\Http\Controllers\Customer\SourcingRequestController::class, 'index'])->name('sourcing-requests.index');
        Route::get('/sourcing-requests/create', [\App\Http\Controllers\Customer\SourcingRequestController::class, 'create'])->name('sourcing-requests.create');
        Route::post('/sourcing-requests', [\App\Http\Controllers\Customer\SourcingRequestController::class, 'store'])->name('sourcing-requests.store');
        Route::get('/sourcing-requests/{sourcingRequest}', [\App\Http\Controllers\Customer\SourcingRequestController::class, 'show'])->name('sourcing-requests.show');
        Route::get('/sourcing-requests/{sourcingRequest}/edit', [\App\Http\Controllers\Customer\SourcingRequestController::class, 'edit'])->name('sourcing-requests.edit');
        Route::put('/sourcing-requests/{sourcingRequest}', [\App\Http\Controllers\Customer\SourcingRequestController::class, 'update'])->name('sourcing-requests.update');
        Route::post('/sourcing-requests/draft', [\App\Http\Controllers\Customer\SourcingRequestController::class, 'saveDraft'])->name('sourcing-requests.draft.store');
        Route::delete('/sourcing-requests/{sourcingRequest}', [\App\Http\Controllers\Customer\SourcingRequestController::class, 'destroy'])->name('sourcing-requests.destroy');

        Route::get('/shipments/create', [\App\Http\Controllers\Customer\ShipmentController::class, 'create'])->name('shipments.create');
        Route::post('/shipments', [\App\Http\Controllers\Customer\ShipmentController::class, 'store'])->name('shipments.store');
        Route::post('/shipments/draft', [\App\Http\Controllers\Customer\ShipmentController::class, 'saveDraft'])->name('shipments.draft.store');
        Route::delete('/shipments/{shipment}', [\App\Http\Controllers\Customer\ShipmentController::class, 'destroy'])->name('shipments.destroy');
        Route::get('/api/carriers', [\App\Http\Controllers\Customer\ShipmentController::class, 'getCarriers'])->name('carriers.api.index');

        Route::get('/shipments/{shipment}', function (\App\Models\Shipment $shipment) {
            abort_if($shipment->sender_id !== auth()->id(), 403);
            $shipment->load('trackingEvents');

            if (Schema::hasTable('shipment_invoices')) {
                $shipment->load('invoices');
            } else {
                $shipment->setRelation('invoices', collect());
            }

            return view('customer.shipments.show', compact('shipment'));
        })->name('shipments.show');

        // Allow customers to print their own documents
        Route::get('/shipments/{shipment}/waybill', [\App\Http\Controllers\Customer\ShipmentPrintController::class, 'printWaybill'])->name('shipments.waybill');
        Route::get('/shipments/{shipment}/invoice', [\App\Http\Controllers\Customer\ShipmentPrintController::class, 'printInvoice'])->name('shipments.invoice');
        Route::get('/shipment-invoices/{shipmentInvoice}/download', [\App\Http\Controllers\Customer\ShipmentInvoiceController::class, 'downloadPdf'])->name('shipment-invoices.download');

        // Sourcing Invoices
        Route::get('/sourcing-invoices/{sourcingInvoice}/download', [\App\Http\Controllers\Customer\SourcingInvoiceController::class, 'downloadPdf'])->name('sourcing-invoices.download');
        Route::get('/sourcing-invoices/{sourcingInvoice}', [\App\Http\Controllers\Customer\SourcingInvoiceController::class, 'show'])->name('sourcing-invoices.show');
        Route::post('/sourcing-invoices/{sourcingInvoice}/pay', [\App\Http\Controllers\Customer\SourcingInvoiceController::class, 'pay'])->name('sourcing-invoices.pay');
    });

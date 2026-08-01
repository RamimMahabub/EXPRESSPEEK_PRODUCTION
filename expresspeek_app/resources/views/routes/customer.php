<?php

use App\Http\Controllers\Customer\DashboardController as CustomerDashboard;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [CustomerDashboard::class, 'index'])->name('dashboard');
        Route::get('/track', [CustomerDashboard::class, 'track'])->name('track');

        // Shipments
        Route::get('/shipments', function () {
            $shipments = auth()->user()->shipments()->with('trackingEvents')->latest()->paginate(15);
            return view('customer.shipments.index', compact('shipments'));
        })->name('shipments.index');

        Route::get('/shipments/{shipment}', function (\App\Models\Shipment $shipment) {
            abort_if($shipment->sender_id !== auth()->id(), 403);
            return view('customer.shipments.show', compact('shipment'));
        })->name('shipments.show');

        // Allow customers to print their own documents
        Route::get('/shipments/{shipment}/invoice', [\App\Http\Controllers\Customer\ShipmentPrintController::class, 'printInvoice'])->name('shipments.invoice');
    });

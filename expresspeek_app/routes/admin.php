<?php

use App\Http\Controllers\Admin\BusinessController as AdminBusinessController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\RateImportController;
use App\Http\Controllers\Admin\ShipmentController as AdminShipmentController;
use App\Http\Controllers\Admin\ShipmentInvoiceController;
use App\Http\Controllers\Admin\SourcingInvoiceController;
use App\Http\Controllers\Admin\SourcingRequestController as AdminSourcingController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Admin\SettingController;

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/dashboard/analytics', [AdminDashboard::class, 'analyticsData'])->name('dashboard.analytics');

        // Safe browser-based deployment utility for cPanel accounts without SSH.
        Route::get('/system-update', function () {
            Artisan::call('migrate:status', ['--no-interaction' => true]);
            $avatarDirectory = public_path('uploads/avatars');

            return view('admin.system-update', [
                'migrationStatus' => Artisan::output(),
                'avatarOptimizerReady' => extension_loaded('gd') && function_exists('imagewebp'),
                'avatarOptimizerDriver' => extension_loaded('gd') ? 'GD' : null,
                'avatarStorageReady' => is_dir($avatarDirectory) && is_writable($avatarDirectory),
                'avatarDatabaseReady' => Schema::hasColumn('users', 'avatar_path'),
                'shipmentInvoiceDatabaseReady' => Schema::hasTable('shipment_invoices'),
                'avatarUploadLimit' => ini_get('upload_max_filesize'),
                'avatarMemoryLimit' => ini_get('memory_limit'),
            ]);
        })->name('system-update');

        Route::post('/system-update', function () {
            Artisan::call('migrate', [
                '--force' => true,
                '--no-interaction' => true,
            ]);
            $migrationOutput = trim(Artisan::output());

            Artisan::call('optimize:clear');

            return redirect()->route('admin.system-update')->with(
                'success',
                $migrationOutput !== '' ? $migrationOutput : 'The database is already up to date.'
            );
        })->middleware('throttle:3,1')->name('system-update.run');

        Route::get('users/search', \App\Http\Controllers\Admin\UserSearchController::class)->name('users.search');
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
        Route::get('/shipments/{shipment}/label', [AdminShipmentController::class, 'printLabel'])->name('shipments.label');
        Route::get('/shipments/{shipment}/waybill', [AdminShipmentController::class, 'printWaybill'])->name('shipments.waybill');
        Route::get('/shipments/{shipment}/invoice', [AdminShipmentController::class, 'printInvoice'])->name('shipments.invoice');
        Route::get('/shipments/{shipment}/invoices/create', [ShipmentInvoiceController::class, 'create'])->name('shipments.invoice.create');
        Route::post('/shipments/{shipment}/invoices', [ShipmentInvoiceController::class, 'store'])->name('shipments.invoice.store');
        Route::patch('/shipment-invoices/{shipmentInvoice}/pay', [ShipmentInvoiceController::class, 'markAsPaid'])->name('shipment-invoices.pay');
        Route::delete('/shipment-invoices/{shipmentInvoice}', [ShipmentInvoiceController::class, 'destroy'])->name('shipment-invoices.destroy');
        Route::get('/shipment-invoices/{shipmentInvoice}/download', [ShipmentInvoiceController::class, 'downloadPdf'])->name('shipment-invoices.download');

        // Sourcing Requests management
        Route::get('/sourcing-requests', [AdminSourcingController::class, 'index'])->name('sourcing-requests.index');
        Route::get('/sourcing-requests/create', [AdminSourcingController::class, 'create'])->name('sourcing-requests.create');
        Route::post('/sourcing-requests', [AdminSourcingController::class, 'store'])->name('sourcing-requests.store');
        Route::get('/sourcing-requests/{sourcingRequest}', [AdminSourcingController::class, 'show'])->name('sourcing-requests.show');
        Route::get('/sourcing-requests/{sourcingRequest}/edit', [AdminSourcingController::class, 'edit'])->name('sourcing-requests.edit');
        Route::put('/sourcing-requests/{sourcingRequest}/details', [AdminSourcingController::class, 'updateDetails'])->name('sourcing-requests.update-details');
        Route::put('/sourcing-requests/{sourcingRequest}', [AdminSourcingController::class, 'update'])->name('sourcing-requests.update');
        Route::patch('/sourcing-requests/{sourcingRequest}/inline', [AdminSourcingController::class, 'updateInline'])->name('sourcing-requests.update-inline');
        Route::delete('/sourcing-requests/{sourcingRequest}', [AdminSourcingController::class, 'destroy'])->name('sourcing-requests.destroy');
        Route::delete('/sourcing-requests/{sourcingRequest}/items/{item}', [AdminSourcingController::class, 'destroyItem'])->name('sourcing-requests.items.destroy');
        Route::post('/sourcing-requests/{sourcingRequest}/items/{item}/reject', [AdminSourcingController::class, 'rejectItem'])->name('sourcing-requests.items.reject');

        // Sourcing Invoices
        Route::get('/sourcing-requests/{sourcingRequest}/invoice', [SourcingInvoiceController::class, 'create'])->name('sourcing-requests.invoice.create');
        Route::post('/sourcing-requests/{sourcingRequest}/invoice', [SourcingInvoiceController::class, 'store'])->name('sourcing-requests.invoice.store');
        Route::get('/sourcing-invoices', [SourcingInvoiceController::class, 'index'])->name('sourcing-invoices.index');
        Route::patch('/sourcing-invoices/{sourcingInvoice}/pay', [SourcingInvoiceController::class, 'markAsPaid'])->name('sourcing-invoices.pay');
        Route::delete('/sourcing-invoices/{sourcingInvoice}', [SourcingInvoiceController::class, 'destroy'])->name('sourcing-invoices.destroy');
        Route::get('/sourcing-invoices/{sourcingInvoice}/download', [SourcingInvoiceController::class, 'downloadPdf'])->name('sourcing-invoices.download');
        
        // Settings
        Route::post('/settings/payment', [SettingController::class, 'updatePaymentSettings'])->name('settings.payment.update');
        
        // Business Applications
        Route::get('/business-applications', [AdminBusinessController::class, 'index'])->name('business-applications.index');
        Route::get('/business-applications/{id}', [AdminBusinessController::class, 'show'])->name('business-applications.show');
    });

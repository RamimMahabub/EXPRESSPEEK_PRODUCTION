<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\SourcingRequestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public homepage — DHL-style landing (guests + all authenticated users)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/track', [HomeController::class, 'track'])->name('track');
Route::get('/shipment/guest/create', [\App\Http\Controllers\PublicShipmentController::class, 'create'])->name('shipment.guest.create');
Route::get('/shipment/carriers', [\App\Http\Controllers\Agent\ShipmentController::class, 'getCarriers'])->name('shipment.carriers.available');
Route::get('/shipment/create', function (\Illuminate\Http\Request $request) {
    if (!auth()->check()) {
        return redirect()->route('login', [
            'next' => route('shipment.create', $request->query(), false),
        ]);
    }

    $user = auth()->user();

    if ($user->isCustomer()) {
        return redirect()->route('customer.dashboard');
    }

    return redirect()->route('agent.shipments.create', $request->query());
})->name('shipment.create');
Route::post('/shipment', [\App\Http\Controllers\PublicShipmentController::class, 'store'])->name('shipment.store');
Route::get('/shipment/{shipment}/invoice', [\App\Http\Controllers\PublicShipmentController::class, 'printInvoice'])
    ->middleware('signed')
    ->name('shipment.invoice');

// Compatibility endpoints for serverless environments where API-prefixed routes can be remapped.
Route::post('/quote', [QuoteController::class, 'index']);

// Sourcing Requests — "Shop from Bangladesh" (public, no login required)
Route::get('/sourcing', [SourcingRequestController::class, 'create'])->name('sourcing.create');
Route::post('/sourcing', [SourcingRequestController::class, 'store'])->name('sourcing.store');
Route::get('/sourcing/success', [SourcingRequestController::class, 'success'])->name('sourcing.success');

// /dashboard redirects admins/agents to their panels; customers and guests go to /
Route::get('/dashboard', function () {
    if (!auth()->check()) return redirect()->route('home');
    $user = auth()->user();
    if ($user->isAdmin()) return redirect()->route('admin.dashboard');
    if ($user->isAgent()) return redirect()->route('agent.dashboard');
    if ($user->isCustomer()) return redirect()->route('customer.dashboard');
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes (all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/customer.php';
require __DIR__.'/agent.php';

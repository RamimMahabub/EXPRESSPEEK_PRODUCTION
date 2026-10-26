<?php

use App\Http\Controllers\Agent\ShipmentController;
use App\Http\Controllers\Api\QuoteController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CityPageController;
use App\Http\Controllers\CountryPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicShipmentController;
use App\Http\Controllers\SeoPageController;
use App\Http\Controllers\SourcingRequestController;
use App\Http\Middleware\SetLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public homepage — DHL-style landing (guests + all authenticated users)
Route::get('/csrf-token', fn () => response()->json(['token' => csrf_token()]))->name('csrf-token');
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/track', [HomeController::class, 'track'])->name('track');
Route::get('/quote', [HomeController::class, 'quote'])->name('quote');
Route::view('/volumetric-weight-calculator', 'tools.volumetric-weight-calculator')
    ->name('volumetric-weight-calculator');
Route::get('/shipment/guest/create', [PublicShipmentController::class, 'create'])->name('shipment.guest.create');
Route::get('/shipment/carriers', [ShipmentController::class, 'getCarriers'])->name('shipment.carriers.available');
Route::get('/shipment/create', function (Request $request) {
    if (! auth()->check()) {
        return redirect()->route('shipment.guest.create', $request->query());
    }

    $user = auth()->user();

    return redirect()->route($user->shipmentCreateRouteName(), $request->query());
})->name('shipment.create');
Route::post('/shipment', [PublicShipmentController::class, 'store'])->name('shipment.store');
Route::get('/shipment/{shipment}/waybill', [PublicShipmentController::class, 'printWaybill'])
    ->middleware('signed')
    ->name('shipment.waybill');
Route::get('/shipment/{shipment}/invoice', [PublicShipmentController::class, 'printInvoice'])
    ->middleware('signed')
    ->name('shipment.invoice');

// Compatibility endpoints for serverless environments where API-prefixed routes can be remapped.
Route::post('/quote', [QuoteController::class, 'index']);

// Sourcing Requests — "Shop from Bangladesh" (public, no login required)
Route::get('/sourcing', [SourcingRequestController::class, 'create'])->name('sourcing.create');
Route::post('/sourcing', [SourcingRequestController::class, 'store'])->name('sourcing.store');
Route::get('/sourcing/success', [SourcingRequestController::class, 'success'])->name('sourcing.success');

// Business Applications
Route::get('/business', [BusinessController::class, 'index'])->name('business.index');
Route::post('/business', [BusinessController::class, 'store'])->name('business.store');

// SEO Pages — Terms, Privacy, Help, Customer Service
Route::get('/terms', [SeoPageController::class, 'terms'])->name('terms');
Route::get('/privacy', [SeoPageController::class, 'privacy'])->name('privacy');
Route::get('/help', [SeoPageController::class, 'help'])->name('help');
Route::get('/customer-service', [SeoPageController::class, 'customerService'])->name('customer-service');

// Country & City Landing Pages (SEO)
Route::get('/ship-to/{country}', [CountryPageController::class, 'show'])->name('ship-to');
Route::get('/ship-from/{city}', [CityPageController::class, 'show'])->name('ship-from');

// Blog / Resources
Route::get('/resources', [BlogController::class, 'index'])->name('blog.index');
Route::get('/resources/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Bangla Localized Routes
Route::prefix('bn')->middleware([SetLocale::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('bn.home');
    Route::get('/track', [HomeController::class, 'track'])->name('bn.track');
    Route::get('/quote', [HomeController::class, 'quote'])->name('bn.quote');
    Route::get('/sourcing', [SourcingRequestController::class, 'create'])->name('bn.sourcing.create');
    Route::get('/terms', [SeoPageController::class, 'terms'])->name('bn.terms');
    Route::get('/privacy', [SeoPageController::class, 'privacy'])->name('bn.privacy');
    Route::get('/help', [SeoPageController::class, 'help'])->name('bn.help');
    Route::get('/customer-service', [SeoPageController::class, 'customerService'])->name('bn.customer-service');
});

// /dashboard redirects admins/agents to their panels; customers and guests go to /
Route::get('/dashboard', function () {
    if (! auth()->check()) {
        return redirect()->route('home');
    }
    return redirect()->route(auth()->user()->dashboardRouteName());
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile and Notification routes (all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/all', [App\Http\Controllers\NotificationController::class, 'all'])->name('notifications.all');
    Route::post('/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
});

// Kept as a safe bookmark for cPanel deployments without terminal access.
// The actual updater is protected by the existing administrator middleware.
Route::get('/cpanel-update', fn () => redirect()->route('admin.system-update'))
    ->middleware(['auth', 'verified', 'role:admin']);

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/customer.php';
require __DIR__.'/agent.php';

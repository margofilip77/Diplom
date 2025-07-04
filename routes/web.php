<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AmenityCategoryController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AmenityController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CartController;
use App\Http\Middleware\MergeSessionCart;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\GuestBookingController;
use App\Http\Controllers\ProviderRegistrationController;

// Головна сторінка
Route::get('/', [HomeController::class, 'index'])->name('home');

// Помешкання
// Помешкання
Route::get('/accommodation', [AccommodationController::class, 'index'])->name('accommodations.index');


Route::get('/accommodations/search', [AccommodationController::class, 'search'])->name('accommodations.search');
Route::get('accommodations/{id}', [AccommodationController::class, 'show'])->name('accommodations.show');


Route::get('accommodations/{id}/meal-options', [AccommodationController::class, 'getMealOptions']);


Route::get('/settlements-by-region', [AccommodationController::class, 'getSettlementsByRegion']);

Route::get('accommodation/{id}', [AmenityController::class, 'show'])->name('accommodation.details');

// Послуги
// Послуги
Route::get('/services/index', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');
Route::get('/services', function () {
    return redirect()->route('services.index');
});

// Пакети (для користувачів)
Route::get('/packages', [PackageController::class, 'index'])->name('packages.select');
Route::get('/packages/{id}', [PackageController::class, 'show'])->name('packages.show');
// Контактна форма

Route::middleware(['auth'])->group(function () {
    Route::get('/contact', [ContactController::class, 'showForm'])->name('contact.form');
    Route::post('/contact/submit', [ContactController::class, 'submit'])->name('contact.submit');
});

Route::get('/admin/support-messages', [ContactController::class, 'adminList'])
    ->name('admin.support-messages')
    ->middleware('auth');
Route::get('/admin/check-unviewed-messages', [ContactController::class, 'checkUnviewedMessages'])
    ->name('admin.check-unviewed-messages')
    ->middleware('auth');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
    ->name('admin.dashboard')
    ->middleware('auth');
    Route::post('/admin/support-messages/{message}/respond', [ContactController::class, 'respond'])
    ->name('admin.support-messages.respond')
    ->middleware('auth');
// Маршрути для кошика
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');

Route::group(['middleware' => [MergeSessionCart::class]], function () {
    Route::get('/cart', [CartController::class, 'showCart'])->name('cart.show');
    Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/get-cart', [CartController::class, 'getCart'])->name('cart.get');
    Route::put('/cart/{cartId}/update', [CartController::class, 'updateCart'])->name('cart.update');
    Route::get('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/store-checkout', [CartController::class, 'storeCheckout'])->name('cart.storeCheckout');
    Route::post('/cart/add-package/{packageId}', [CartController::class, 'addPackage'])->name('cart.addPackage');
});
// Оформлення замовлення
Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
Route::post('/checkout', [CartController::class, 'storeCheckout'])->name('checkout.store');

// Маршрути для улюблених
Route::middleware('auth')->group(function () {
    Route::post('/favorites/toggle/{accommodation}', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/profile/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');
});
Route::delete('/user/support-messages/{message}', [ProfileController::class, 'deleteSupportMessage'])
    ->name('user.support-messages.delete')
    ->middleware('auth');
// Відгуки
Route::middleware(['auth'])->group(function () {
    Route::post('/accommodations/{accommodationId}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
});
// Профіль користувача
// Маршрути для авторизації та реєстрації надавачів
Route::get('/provider/auth', [ProviderController::class, 'showProviderAuth'])->name('provider.auth');
Route::get('/provider/register', [ProviderController::class, 'register'])->name('provider.register');
Route::post('/provider/register', [ProviderController::class, 'register']); // Прибрано з middleware auth
Route::post('/provider/login', [ProviderController::class, 'login'])->name('provider.login');


// Маршрути для профілю (залишаємо middleware auth для інших маршрутів)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');
    Route::post('/profile/favorites/{accommodationId}', [ProfileController::class, 'toggleFavorite'])->name('profile.toggle-favorite');
    Route::get('/profile/bookings', [ProfileController::class, 'bookings'])->name('profile.bookings');
    Route::delete('/booking/{booking}/cancel', [ProfileController::class, 'cancelBooking'])->name('booking.cancel');
    Route::patch('/booking/{booking}/update-dates', [ProfileController::class, 'updateDates'])->name('booking.update.dates');
});

// Маршрути авторизації
Auth::routes();

// Маршрути для надавача
Route::prefix('provider')->group(function () {
    Route::get('/dashboard', [ProviderController::class, 'dashboard'])->name('provider.dashboard');
    Route::get('/orders', [ProviderController::class, 'orders'])->name('provider.orders');
    Route::get('/services/create', [ProviderController::class, 'createService'])->name('provider.services.create');
    Route::post('/services', [ProviderController::class, 'storeService'])->name('provider.services.store');
    Route::get('/services', function () {
        return redirect()->route('provider.services.create');
    });
    Route::get('/services/{service}/edit', [ProviderController::class, 'editService'])->name('provider.services.edit');
    Route::put('/services/{service}', [ProviderController::class, 'updateService'])->name('provider.services.update');
    Route::post('/services/{service}/resubmit', [ProviderController::class, 'resubmitService'])->name('provider.services.resubmit');
    Route::delete('/services/{service}', [ProviderController::class, 'destroyService'])->name('provider.services.destroy');
    Route::get('/accommodations/create', [ProviderController::class, 'createAccommodation'])->name('provider.accommodations.create');
    Route::post('/accommodations', [ProviderController::class, 'storeAccommodation'])->name('provider.accommodations.store');
    Route::get('/accommodations/{accommodation}/edit', [ProviderController::class, 'editAccommodation'])->name('provider.accommodations.edit');
    Route::put('/accommodations/{accommodation}', [ProviderController::class, 'updateAccommodation'])->name('provider.accommodations.update');
    Route::post('/accommodations/{accommodation}/resubmit', [ProviderController::class, 'resubmitAccommodation'])->name('provider.accommodations.resubmit');
    Route::delete('/accommodations/{accommodation}', [ProviderController::class, 'destroyAccommodation'])->name('provider.accommodations.destroy');
});

// Маршрути адмін-панелі
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users/{user}/toggle-block', [AdminController::class, 'toggleBlockUser'])->name('admin.users.toggle-block');
    Route::patch('/admin/users/{user}/update-role', [AdminController::class, 'updateRole'])->name('admin.users.update-role');
    Route::get('/admin/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::delete('/admin/reviews/{review}', [AdminController::class, 'deleteReview'])->name('admin.reviews.delete');
    Route::post('/admin/reviews/{review}/reject', [AdminController::class, 'rejectReview'])->name('admin.reviews.reject');
    Route::get('/admin/pending-offers', [AdminController::class, 'pendingOffers'])->name('admin.pending-offers');
    Route::post('/admin/services/{service}/approve', [AdminController::class, 'approveService'])->name('admin.services.approve');
    Route::post('/admin/services/{service}/reject', [AdminController::class, 'rejectService'])->name('admin.services.reject');
    Route::post('/admin/accommodations/{accommodation}/approve', [AdminController::class, 'approveAccommodation'])->name('admin.accommodations.approve');
    Route::post('/admin/accommodations/{accommodation}/reject', [AdminController::class, 'rejectAccommodation'])->name('admin.accommodations.reject');
    Route::get('/admin/packages', [AdminController::class, 'packages'])->name('admin.packages');
    Route::get('/admin/packages/create', [AdminController::class, 'createPackage'])->name('admin.packages.create');
    Route::post('/admin/packages', [AdminController::class, 'storePackage'])->name('admin.packages.store');
    Route::get('/admin/packages/{id}/edit', [AdminController::class, 'editPackage'])->name('admin.packages.edit');
    Route::put('/admin/packages/{id}', [AdminController::class, 'updatePackage'])->name('admin.packages.update');
    Route::delete('/admin/packages/{id}', [AdminController::class, 'deletePackage'])->name('admin.packages.delete');
    Route::delete('/admin/support-messages/{message}', [ContactController::class, 'delete'])
        ->name('admin.support-messages.delete')
        ->middleware('auth');
    Route::patch('/admin/reviews/{review}/block', [AdminController::class, 'blockReview'])->name('admin.reviews.block');
});
Route::get('/admin/check-recent-messages', [ContactController::class, 'checkRecentMessages'])
    ->name('admin.check-recent-messages')
    ->middleware('auth');
Route::get('/guest/success', [GuestBookingController::class, 'success'])->name('guest.success');
Route::post('/booking/attach', [GuestBookingController::class, 'attachBooking'])->name('booking.attach')->middleware('auth');

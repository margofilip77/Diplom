<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TourController;
use App\Http\Controllers\AccommodationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AmenityCategoryController;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
});


Route::get('/accommodation', [AccommodationController::class, 'index'])->name('accommodations.accommodation');


Route::get('/accommodations/search', [AccommodationController::class, 'search'])->name('accommodations.search');
Route::get('accommodations/{id}', [AccommodationController::class, 'show'])->name('accommodations.show');



use App\Http\Controllers\AmenityController;

Route::get('accommodation/{id}', [AmenityController::class, 'show'])->name('accommodation.details');

use App\Http\Controllers\HomeController;


Route::get('/', [HomeController::class, 'index'])->name('home'); // Головна сторінка

use App\Http\Controllers\ServiceController;
Route::get('/services/index', [ServiceController::class, 'index'])->name('services');


Route::get('/service/{id}', [ServiceController::class, 'show'])->name('service.show'); // Сторінка конкретної послуги


Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');


Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');



// Видалити товар з кошика
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/cart-debug', function () {
    return response()->json(session()->get('cart'));
});

require __DIR__.'/auth.php';

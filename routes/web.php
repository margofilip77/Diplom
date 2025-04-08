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
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('/accommodation', [AccommodationController::class, 'index'])->name('accommodations.accommodation');


Route::get('/accommodations/search', [AccommodationController::class, 'search'])->name('accommodations.search');
Route::get('accommodations/{id}', [AccommodationController::class, 'show'])->name('accommodations.show');


Route::get('accommodations/{id}/meal-options', [AccommodationController::class, 'getMealOptions']);


use App\Http\Controllers\AmenityController;

Route::get('accommodation/{id}', [AmenityController::class, 'show'])->name('accommodation.details');



Route::get('/', [HomeController::class, 'index'])->name('home'); // Головна сторінка

use App\Http\Controllers\ServiceController;
Route::get('/services/index', [ServiceController::class, 'index'])->name('services');


Route::get('/service/{id}', [ServiceController::class, 'show'])->name('service.show'); // Сторінка конкретної послуги


Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');




// Для API
// Для API
// В файлі routes/web.php або routes/api.php
use App\Http\Controllers\CartController;

Route::get('/cart', [CartController::class, 'showCart'])->name('cart.show');

    Route::post('/add-to-cart', [CartController::class, 'add']);

    Route::get('/get-cart', [CartController::class, 'getCart']);
    Route::get('/cart/{cartId}', [CartController::class, 'showCart'])->name('cart.show');

    Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::put('/cart/{cartId}/update', [CartController::class, 'updateCart'])->name('cart.update');



require __DIR__.'/auth.php';

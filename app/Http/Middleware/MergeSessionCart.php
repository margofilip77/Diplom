<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Cart;
use App\Models\CartAccommodation;
use App\Models\CartAccommodationMealOption;

class MergeSessionCart
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::where('user_id', $userId)->first();
            $sessionCart = Session::get('cart', []);

            if (!empty($sessionCart)) {
                if (!$cart) {
                    $cart = Cart::create(['user_id' => $userId]);
                    \Illuminate\Support\Facades\Log::info('Created new cart for user', [
                        'user_id' => $userId,
                        'cart_id' => $cart->id,
                    ]);
                }

                foreach ($sessionCart as $key => $item) {
                    if (!isset($item['accommodation_id'], $item['checkin_date'], $item['checkout_date'], $item['guests_count'], $item['accommodation_photo'], $item['price'])) {
                        \Illuminate\Support\Facades\Log::warning('Invalid session cart item', ['item' => $item]);
                        continue;
                    }

                    // Нормалізація guests_count
                    $guestsCount = $item['guests_count'];
                    ksort($guestsCount);
                    $guestsCountJson = json_encode($guestsCount);

                    // Перевірка на унікальність
                    $exists = $cart->accommodations()
                        ->where('accommodation_id', $item['accommodation_id'])
                        ->where('checkin_date', $item['checkin_date'])
                        ->where('checkout_date', $item['checkout_date'])
                        ->where('guests_count', $guestsCountJson)
                        ->exists();

                    if ($exists) {
                        \Illuminate\Support\Facades\Log::info('Duplicate accommodation found in DB, skipping merge', [
                            'user_id' => $userId,
                            'accommodation_id' => $item['accommodation_id'],
                            'checkin_date' => $item['checkin_date'],
                            'checkout_date' => $item['checkout_date'],
                            'guests_count' => $guestsCount,
                        ]);
                        continue;
                    }

                    // Додаємо до бази даних
                    $cartAccommodation = new CartAccommodation([
                        'cart_id' => $cart->id,
                        'accommodation_id' => $item['accommodation_id'],
                        'checkin_date' => $item['checkin_date'],
                        'checkout_date' => $item['checkout_date'],
                        'guests_count' => $guestsCountJson,
                        'accommodation_photo' => $item['accommodation_photo'],
                        'price' => $item['price'],
                    ]);
                    $cartAccommodation->save();

                    \Illuminate\Support\Facades\Log::info('Merged session item to DB cart', [
                        'cart_accommodation_id' => $cartAccommodation->id,
                        'accommodation_id' => $item['accommodation_id'],
                    ]);

                    // Додаємо meal_options
                    if (!empty($item['meal_options'])) {
                        foreach ($item['meal_options'] as $mealOption) {
                            if (isset($mealOption['meal_option_id'], $mealOption['guests_count'])) {
                                CartAccommodationMealOption::create([
                                    'cart_accommodation_id' => $cartAccommodation->id,
                                    'meal_option_id' => $mealOption['meal_option_id'],
                                    'guests_count' => $mealOption['guests_count'],
                                    'price' => $mealOption['price'] ?? 0,
                                ]);
                            }
                        }
                        \Illuminate\Support\Facades\Log::info('Merged meal options to DB', [
                            'cart_accommodation_id' => $cartAccommodation->id,
                            'meal_options' => $item['meal_options'],
                        ]);
                    }
                }

                // Очищаємо сесію
                Session::forget('cart');
                \Illuminate\Support\Facades\Log::info('Cleared session cart after merge', ['user_id' => $userId]);
            }
        }

        return $response;
    }
}
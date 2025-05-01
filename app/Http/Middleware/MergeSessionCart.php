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
        // Виконуємо перевірку після авторизації
        $response = $next($request);

        // Якщо користувач щойно увійшов в акаунт
        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::where('user_id', $userId)->first();

            // Отримуємо кошик із сесії
            $sessionCart = Session::get('cart', []);

            if (!empty($sessionCart)) {
                if (!$cart) {
                    // Якщо кошика немає в базі, створюємо новий
                    $cart = Cart::create(['user_id' => $userId]);
                }

                // Переносимо елементи із сесії до бази
                foreach ($sessionCart as $key => $item) {
                    // Перевіряємо наявність обов’язкових ключів
                    if (!isset($item['accommodation_id'], $item['checkin_date'], $item['checkout_date'], $item['guests_count'], $item['accommodation_photo'], $item['price'])) {
                        \Illuminate\Support\Facades\Log::warning('Invalid session cart item: ' . json_encode($item));
                        continue; // Пропускаємо елемент, якщо він не містить усіх необхідних даних
                    }

                    $cartAccommodation = new CartAccommodation([
                        'cart_id' => $cart->id,
                        'accommodation_id' => $item['accommodation_id'],
                        'checkin_date' => $item['checkin_date'],
                        'checkout_date' => $item['checkout_date'],
                        'guests_count' => json_encode($item['guests_count']),
                        'accommodation_photo' => $item['accommodation_photo'],
                        'price' => $item['price'],
                    ]);
                    $cartAccommodation->save();

                    // Переносимо meal_options, якщо вони є
                    if (!empty($item['meal_options'])) {
                        foreach ($item['meal_options'] as $mealOption) {
                            if (isset($mealOption['meal_option_id'], $mealOption['guests_count'])) {
                                CartAccommodationMealOption::create([
                                    'cart_accommodation_id' => $cartAccommodation->id,
                                    'meal_option_id' => $mealOption['meal_option_id'],
                                    'guests_count' => $mealOption['guests_count'],
                                ]);
                            }
                        }
                    }
                }

                // Очищаємо кошик у сесії після перенесення
                Session::forget('cart');
            }
        }

        return $response;
    }
}
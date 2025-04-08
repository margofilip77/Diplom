<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\MealOption;
use App\Models\CartMeal;
use Illuminate\Http\Request;
use App\Models\Accommodation;
use Illuminate\Support\Facades\Auth;
use App\Models\CartAccommodation;

class CartController extends Controller
{
    // Отримати всі кошики користувача

    // Отримати всі кошики користувача
    public function index()
    {
        $carts = Cart::where('user_id', 7)->with('mealOptions')->get();
        return response()->json($carts);
    }
    public function add(Request $request)
    {
        // Тимчасово ставимо ID користувача (наприклад, 1)
        $userId = 7; // Замінити на тимчасовий ID користувача

        // Створюємо або отримуємо кошик для користувача з тимчасовим ID
        $cart = Cart::where('user_id', $userId)->first();

        // Якщо кошик не знайдений, створюємо новий
        if (!$cart) {
            $cart = Cart::create(['user_id' => $userId]);
        }

        // Отримуємо інформацію про помешкання
        $accommodation = Accommodation::findOrFail($request->accommodation_id);

        // Отримуємо шлях до фото помешкання (перше фото)
        $accommodationPhoto = $accommodation->photos()->first()->photo_path ?? null;

        if (!$accommodationPhoto) {
            return response()->json(['error' => 'Фото не знайдено для цього помешкання'], 404);
        }

        // Додаємо помешкання до кошика (таблиця cart_accommodation)
        $cartAccommodation = $cart->accommodations()->create([
            'accommodation_id' => $accommodation->id,
            'checkin_date' => $request->checkin_date,
            'checkout_date' => $request->checkout_date,
            'guests_count' => json_encode($request->guests_count), // перетворюємо кількість гостей у формат JSON
            'accommodation_photo' => $accommodationPhoto,
            'price' => $accommodation->price_per_night,
        ]);

        // Додаємо вибрані типи харчування до кошика (таблиця cart_accommodation_meal_option)
        if (isset($request->meal_options)) {
            foreach ($request->meal_options as $mealOption) {
                $cartAccommodation->mealOptions()->create([
                    'meal_option_id' => $mealOption['meal_option_id'],
                    'guests_count' => $mealOption['guests_count'],
                ]);
            }
        }

        return response()->json(['success' => 'Помешкання та харчування додано до кошика']);
    }





    public function showCart()
    {
        $userId = 7; // Тимчасово фіксований ID користувача

        // Отримуємо кошик з усіма пов’язаними даними
        $cart = Cart::with(['accommodations.accommodation', 'accommodations.mealOptions.mealOption'])
            ->where('user_id', $userId)
            ->first();

        if (!$cart) {
            return view('cart.index', ['cart' => null]);
        }

        return view('cart.index', ['cart' => $cart]);
    }




    public function show($cartId)
    {
        // Отримуємо кошик з усіма даними, включаючи mealOptions
        $cart = Cart::with('mealOptions')->find($cartId);

        // Якщо кошик не знайдено
        if (!$cart) {
            return view('cart.empty');
        }

        // Перетворюємо кількість гостей із JSON в звичайний вигляд
        $guestsCount = json_decode($cart->guests_count, true);

        // Підраховуємо загальну вартість
        $totalPrice = $cart->mealOptions->sum(function ($mealOption) {
            return $mealOption->price * $mealOption->pivot->guests_count;
        });

        // Відправляємо дані в представлення
        return view('cart.index', [
            'cart' => $cart,
            'totalPrice' => $totalPrice,
            'guestsCount' => $guestsCount
        ]);
    }
    public function getCart(Request $request)
    {
        // Припускаємо, що користувач має ID 7 для тесту
        $userId = 7; // Замість цього потрібно використовувати ID реального користувача
        $cart = Cart::where('user_id', $userId)->get();

        return response()->json($cart);
    }


    public function remove($id)
    {
        // Отримуємо кошик користувача (можна отримати за поточним користувачем)
        $userId = 7; // Замінити на ID користувача, який авторизований
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            // Знаходимо запис у кошику, який потрібно видалити
            $cartAccommodation = $cart->accommodations()->where('id', $id)->first();

            if ($cartAccommodation) {
                // Спочатку видаляємо типи харчування, пов'язані з цим помешканням
                $cartAccommodation->mealOptions()->delete(); // Видаляємо типи харчування для цього помешкання

                // Тепер видаляємо саме помешкання з кошика
                $cartAccommodation->delete();

                // Перенаправлення на сторінку кошика
                return redirect()->route('cart.show', ['cartId' => $cart->id])->with('success', 'Помешкання та обрані типи харчування видалено з кошика');
            }
        }

        return redirect()->route('cart.show', ['cartId' => $cart->id])->with('error', 'Помешкання не знайдено в кошику');
    }



    public function updateCart(Request $request, $cartId)
    {
        // Знайдемо кошик
        $cartAccommodation = CartAccommodation::find($cartId);

        // Перевірка, чи кошик існує
        if (!$cartAccommodation) {
            return redirect()->route('cart.index')->with('error', 'Кошик не знайдений');
        }

        // Очищення попередніх варіантів харчування для цього кошика
        $cartAccommodation->mealOptions()->detach();

        // Перевіряємо, чи були вибрані типи харчування
        if ($request->has('meal_option')) {
            foreach ($request->meal_option as $mealId => $guestsCount) {
                if ($guestsCount > 0) {
                    // Знайти вибраний тип харчування
                    $mealOption = MealOption::find($mealId);

                    // Перевірка, чи тип харчування існує
                    if ($mealOption) {
                        // Додаємо новий запис до таблиці cart_accommodation_meal_option
                        $cartAccommodation->mealOptions()->attach($mealOption->id, [
                            'guests_count' => $guestsCount,
                            'price' => $mealOption->price,  // якщо потрібно зберігати ціну
                        ]);
                    }
                }
            }
        }

        // Повернення на сторінку кошика з успішним повідомленням
        return redirect()->route('cart.index')->with('success', 'Кошик оновлено');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartMealOption;
use App\Models\MealOption;
use App\Models\Accommodation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Додайте фасад для Auth

class CartController extends Controller
{
    public function addToCart(Request $request)
{
    // Перевірка, чи користувач авторизований
    if (!Auth::check()) {
        return response()->json(['message' => 'Будь ласка, увійдіть в систему'], 401);
    }

    // Перевірка наявності помешкання
    $accommodation = Accommodation::findOrFail($request->accommodation_id);

    // Отримуємо перше фото помешкання
    $photoUrl = $accommodation->photos->first()->url ?? null;

    // Розраховуємо загальну ціну
    $totalPrice = $accommodation->price_per_night * $request->guests;

    // Створення нового запису в кошику
    $cart = Cart::create([
        'user_id' => Auth::id(), // Отримуємо ID авторизованого користувача
        'accommodation_id' => $accommodation->id,
        'guests' => $request->guests,
        'checkin_date' => $request->checkin_date,
        'checkout_date' => $request->checkout_date,
        'total_price' => $totalPrice,
        'photo_url' => $photoUrl,
    ]);

    // Перевірка, чи є варіанти харчування
    if ($request->has('meal_options')) {
        foreach ($request->meal_options as $mealOptionId => $guestCount) {
            // Перевірка, чи правильний тип харчування
            $mealOption = MealOption::find($mealOptionId);
            if ($mealOption) {
                // Зберігаємо тип харчування для кожного гостя
                CartMealOption::create([
                    'cart_id' => $cart->id,
                    'meal_option_id' => $mealOptionId,
                    'guests_count' => $guestCount,
                ]);
            }
        }
    }

    return response()->json(['message' => 'Помешкання додано в кошик', 'cart' => $cart]);
}


    public function showCart()
    {
        // Використовуємо Auth для отримання користувача
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login'); // Якщо користувач не увійшов, редиректимо на сторінку входу
        }

        // Отримуємо елементи кошика для авторизованого користувача
        $cartItems = Cart::where('user_id', $user->id)->with('accommodation')->get();
        
        return view('cart', compact('cartItems'));
    }
    public function index()
{
    $cartItems = Cart::where('user_id', Auth::id())->get();

    // Якщо потрібно передати дані у вигляді масиву, можна зробити наступне:
    $cart = $cartItems->mapWithKeys(function($item) {
        return [$item->accommodation_id => [
            'name' => $item->accommodation->name,
            'quantity' => 1, // Якщо зберігається лише одне значення, якщо кількість товарів різна, потрібно додати відповідну логіку
            'total_price' => $item->price
        ]];
    });

    return view('cart.index', compact('cart'));
}

}


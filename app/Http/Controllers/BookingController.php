<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\MealOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Accommodation;

class BookingController extends Controller
{
    /**
     * Зберігає нове бронювання.
     */
    public function store(Request $request)
    {
        // Валідація вхідних даних
        $request->validate([
            'accommodation_id' => 'required|exists:accommodations,id',
            'meal_option' => 'nullable|exists:meal_options,id',
        ]);

        // Отримуємо meal_option, якщо він є
        $mealOptionId = $request->meal_option ?: null;

        // Створюємо нове бронювання
        $booking = Booking::create([
            'accommodation_id' => $request->accommodation_id,
            'user_id' => Auth::id(), // Отримуємо ID користувача
            'meal_option_id' => $mealOptionId, // Якщо є, додаємо meal_option
        ]);

        // Перенаправляємо користувача на сторінку успішного бронювання
        return redirect()->route('booking.success', $booking->id)
            ->with('booking', $booking) // Передаємо інформацію про бронювання
            ->with('success', 'Бронювання успішне!');
    }

    /**
     * Показує деталі конкретного помешкання.
     */
    public function show($id)
    {
        // Знайдемо помешкання за ID
        $accommodation = Accommodation::findOrFail($id);
 // Отримуємо список варіантів харчування для цього помешкання
 $mealOptions = $accommodation->mealOptions;
        // Якщо користувач вже зробив бронювання для цього помешкання
        $booking = Booking::where('accommodation_id', $accommodation->id)
        ->where('user_id', Auth::id())
                          ->first(); // Знайдемо перше бронювання для користувача та помешкання

        // Повертаємо view з передачею даних
        return view('accommodations.details', compact('accommodation', 'booking'));
    }
    
}

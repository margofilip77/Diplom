<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use App\Models\Favorite;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use App\Models\SupportMessage;

class ProfileController extends Controller
{
    /**
     * Відображення сторінки редагування профілю.
     */
    public function edit(): View
    {
        /** @var User $user */
        $user = Auth::user();

        // Завантаження зв’язків
        $user = User::with('favorites.accommodation')->find($user->id);

        // Завантажуємо повідомлення служби підтримки для поточного користувача
        $messages = SupportMessage::where('email', $user->email)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile.edit', compact('user', 'messages'));
    }

    /**
     * Оновлення інформації профілю.
     */
    public function update(Request $request): RedirectResponse
    {
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Ваш обліковий запис заблоковано. Ви не можете редагувати профіль.');
        }
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        // Обробка аватара
        if ($request->hasFile('avatar')) {
            // Видаляємо старий аватар, якщо він є
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Оновлення пароля користувача.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Ваш обліковий запис заблоковано. Ви не можете оновлювати пароль.');
        }
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return redirect()->route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Видалення облікового запису.
     */
    public function destroy(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        // Перевірка пароля
        $request->validate([
            'password' => ['required', 'string', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Неправильний пароль.');
                }
            }],
        ], [
            'password.required' => 'Пароль є обов’язковим.',
        ]);

        // Очищення пов’язаних даних
        Favorite::where('user_id', $user->id)->delete();
        Booking::where('user_id', $user->id)->delete();
        SupportMessage::where('email', $user->email)->delete();
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Видалення користувача
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'account-deleted');
    }

    /**
     * Відображення улюблених помешкань.
     */
    public function favorites()
    {
        $user = Auth::user();
        $favorites = Favorite::where('user_id', $user->id)
            ->with('accommodation')
            ->get();

        return view('profile.favorites', compact('favorites'));
    }
    public function toggleFavorite(Request $request, $accommodationId): RedirectResponse
    {
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Ваш обліковий запис заблоковано. Ви не можете додавати або видаляти улюблені помешкання.');
        }

        $user = Auth::user();
        $favorite = Favorite::where('user_id', $user->id)
            ->where('accommodation_id', $accommodationId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Помешкання видалено з улюблених.';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'accommodation_id' => $accommodationId,
            ]);
            $message = 'Помешкання додано до улюблених.';
        }

        return redirect()->back()->with('status', $message);
    }
    public function bookings()
    {
        $user = Auth::user();
        $bookings = Booking::where('user_id', $user->id)
            ->with(['accommodation.amenities', 'services.service', 'packages.package', 'mealOptions']) // Додаємо mealOptions
            ->get();
    
        $totalAmount = $bookings->sum('total_price');
    
        return view('profile.bookings', compact('bookings', 'totalAmount'));
    }
    public function cancelBooking(Booking $booking)
    {
        // Перевірка, чи належить бронювання користувачу
        if ($booking->user_id !== Auth::id()) {
            return redirect()->route('profile.bookings')->with('error', 'Ви не маєте доступу до цього бронювання.');
        }

        // Перевірка, чи можна скасувати (більше 14 діб)
        $checkinDate = \Carbon\Carbon::parse($booking->checkin_date);
        $currentDate = \Carbon\Carbon::now();
        $daysUntilCheckin = $currentDate->diffInDays($checkinDate, false);

        if ($daysUntilCheckin <= 14) {
            return redirect()->route('profile.bookings')->with('error', 'Скасувати бронювання можна лише за 14 діб до заїзду.');
        }

        // Скасування бронювання (видалення)
        $booking->delete();
        Log::info('Booking cancelled', [
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('profile.bookings')->with('success', 'Бронювання успішно скасовано.');
    }

    public function updateDates(Request $request, Booking $booking)
    {
        // Перевірка, чи належить бронювання користувачу
        if ($booking->user_id !== Auth::id()) {
            return redirect()->route('profile.bookings')->with('error', 'Ви не маєте доступу до цього бронювання.');
        }
    
        // Перевірка, чи можна змінювати дати (більше 14 діб)
        $checkinDate = \Carbon\Carbon::parse($booking->checkin_date);
        $currentDate = \Carbon\Carbon::now();
        $daysUntilCheckin = $currentDate->diffInDays($checkinDate, false);
    
        if ($daysUntilCheckin <= 14) {
            return redirect()->route('profile.bookings')->with('error', 'Змінити дати можна лише за 14 діб до заїзду.');
        }
    
        // Валідація нових дат і нової суми
        $request->validate([
            'checkin_date' => 'required|date|after_or_equal:today',
            'checkout_date' => 'required|date|after:checkin_date',
            'new_total_price' => 'required|numeric|min:0', // Валідація нової суми
        ]);
    
        // Оновлення дат і загальної суми
        $booking->update([
            'checkin_date' => $request->checkin_date,
            'checkout_date' => $request->checkout_date,
            'total_price' => $request->new_total_price, // Оновлюємо total_price у базі
        ]);
    
        Log::info('Booking dates updated', [
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'new_checkin_date' => $request->checkin_date,
            'new_checkout_date' => $request->checkout_date,
            'new_total_price' => $request->new_total_price,
        ]);
    
        return redirect()->route('profile.bookings')->with('success', 'Дати бронювання успішно змінено.');
    }
    // Додаємо метод для видалення повідомлення користувачем
    public function deleteSupportMessage(Request $request, SupportMessage $message)
    {
        // Перевіряємо, чи належить повідомлення користувачу
        if ($message->email !== Auth::user()->email) {
            return redirect()->route('profile.edit')->with('error', 'Ви не маєте доступу до цього повідомлення.');
        }

        $message->delete();
        return redirect()->route('profile.edit')->with('status', 'message-deleted');
    }
}
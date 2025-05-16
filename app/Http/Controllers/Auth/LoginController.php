<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
    

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }
    // Показати форму входу
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/home'); // Змінити '/home' на вашу домашню сторінку
        }

        return view('auth.login');
    }
    protected function authenticated(Request $request, $user)
    {
        // Прив’язка бронювань за email
        $guestEmail = Session::get('guest_email');
        $bookingIds = Session::get('booking_ids', []);

        if ($guestEmail && $guestEmail === $user->email) {
            $bookings = Booking::where('email', $user->email)
                ->whereIn('id', $bookingIds)
                ->whereNull('user_id')
                ->get();

            if ($bookings->isNotEmpty()) {
                foreach ($bookings as $booking) {
                    $booking->update([
                        'user_id' => $user->id,
                        'token' => null,
                    ]);
                    Log::info('Booking attached to user after login', [
                        'user_id' => $user->id,
                        'booking_id' => $booking->id,
                        'email' => $user->email,
                    ]);
                }
            } else {
                Log::info('No bookings found for email after login', [
                    'email' => $user->email,
                    'booking_ids' => $bookingIds,
                ]);
            }

            // Очищаємо сесію після прив’язки
            Session::forget('guest_email');
            Session::forget('booking_ids');
        } else {
            Log::warning('Guest email does not match or is not set', [
                'guest_email' => $guestEmail,
                'user_email' => $user->email,
            ]);
        }

        return redirect()->intended($this->redirectTo());
    }

    protected function redirectTo()
    {
        $user = Auth::user();
        if ($user->role === 'admin') {
            return route('admin.dashboard');
        } elseif ($user->role === 'provider') {
            return route('provider.dashboard');
        }
        return route('home'); // Для звичайних користувачів
    }
}

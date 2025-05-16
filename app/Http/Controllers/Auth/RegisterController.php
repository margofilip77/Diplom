<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        // Прив’язка бронювань за email
        $guestEmail = Session::get('guest_email');
        $bookingIds = Session::get('booking_ids', []);

        if ($guestEmail && $guestEmail === $data['email']) {
            $bookings = Booking::where('email', $data['email'])
                ->whereIn('id', $bookingIds)
                ->whereNull('user_id')
                ->get();

            if ($bookings->isNotEmpty()) {
                foreach ($bookings as $booking) {
                    $booking->update([
                        'user_id' => $user->id,
                        'token' => null,
                    ]);
                    Log::info('Booking attached to user during registration', [
                        'user_id' => $user->id,
                        'booking_id' => $booking->id,
                        'email' => $data['email'],
                    ]);
                }
            } else {
                Log::info('No bookings found for email during registration', [
                    'email' => $data['email'],
                    'booking_ids' => $bookingIds,
                ]);
            }

            // Очищаємо сесію після прив’язки
            Session::forget('guest_email');
            Session::forget('booking_ids');
        } else {
            Log::warning('Guest email does not match or is not set', [
                'guest_email' => $guestEmail,
                'provided_email' => $data['email'],
            ]);
        }

        event(new Registered($user));
        $this->guard()->login($user);

        return $user;
    }
}

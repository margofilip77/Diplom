<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuestBookingController extends Controller
{
    public function success(Request $request)
    {
        $successMessage = $request->session()->get('success');
        return view('guest.success', compact('successMessage'));
    }

    public function attachBooking(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:bookings,email',
            'token' => 'required|string|exists:bookings,token',
        ]);

        $booking = Booking::where('email', $request->email)
            ->where('token', $request->token)
            ->whereNull('user_id')
            ->firstOrFail();

        $booking->update([
            'user_id' => Auth::id(),
            'token' => null, // Очищаємо токен після прив’язки
        ]);

        return redirect()->route('profile.bookings')->with('success', 'Ваше бронювання успішно прив’язано до акаунта!');
    }
}
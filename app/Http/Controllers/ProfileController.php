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
        return view('profile.edit', compact('user'));
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
}
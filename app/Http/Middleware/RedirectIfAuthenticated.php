<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard'); // Перенаправлення для адміна
                } elseif ($user->role === 'provider') {
                    return redirect()->route('provider.dashboard'); // Перенаправлення для надавача
                }
                return redirect('/dashboard'); // За замовчуванням (можна видалити, якщо ролі обмежені)
            }
        }

        return $next($request);
    }
}
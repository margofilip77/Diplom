<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Provider
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role === 'provider') {
            return $next($request);
        }

        abort(403, 'Unauthorized action. You must be a provider to access this page.');
    }
}
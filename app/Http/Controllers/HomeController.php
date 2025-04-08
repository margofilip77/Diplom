<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Перевірка чи користувач авторизований
        $user = Auth::user();

        // Якщо користувач не авторизований, редиректимо на сторінку входу
        if (!$user) {
            return redirect()->route('login');
        }

        // Передаємо змінну $user у шаблон
        return view('home', compact('user'));
    }

}

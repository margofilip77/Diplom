<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Package;
use App\Models\Accommodation;
use App\Models\Service;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Прибираємо middleware 'auth', щоб неавторизовані користувачі могли бачити головну сторінку
        // Middleware 'auth' буде застосовуватися лише до маршрутів, які цього потребують
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Популярні помешкання (сортуємо за середнім рейтингом)
        $popularAccommodations = Accommodation::select('id', 'name', 'description', 'price_per_night')
            ->with(['reviews', 'photos']) // Завантажуємо відгуки та фото
            ->get()
            ->map(function ($accommodation) {
                $accommodation->average_rating = $accommodation->reviews->avg('rating') ?? 0;
                return $accommodation;
            })
            ->sortByDesc('average_rating')
            ->take(6);

        // Популярні послуги (просто перші 6)
        $popularServices = Service::select('id', 'name', 'description', 'price', 'image')
            ->take(6)
            ->get();

        return view('home', compact('popularAccommodations', 'popularServices'));
    }
}
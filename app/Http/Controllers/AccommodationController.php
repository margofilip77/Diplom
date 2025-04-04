<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\Request;
use App\Models\AccommodationPhoto;
use Illuminate\Support\Facades\DB;
use App\Models\MealOption;

class AccommodationController extends Controller
{
    public function index()
    {
        $accommodations = Accommodation::all(); // Отримати всі помешкання
        return view('accommodations.index', compact('accommodations'));
    }

    public function show($id)
    {
        $accommodation = Accommodation::with('amenities')->findOrFail($id);
        return view('accommodations.details', compact('accommodation'));
    }
    
    public function search(Request $request)
    {
        $query = Accommodation::query();

        // Фільтрація лише за заповненими полями
        if ($request->filled('region')) {
            $query->where('region', 'LIKE', '%' . $request->location . '%');
        }

        if ($request->filled('check_in') && $request->filled('check_out')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->whereBetween('date', [$request->check_in, $request->check_out]);
            });
        }

        if ($request->filled('guests')) {
            $query->where('capacity', '>=', $request->guests);
        }

        // Отримуємо результати
        $accommodations = $query->get();

        return view('accommodations.search_results', compact('accommodations'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'main_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        // Збереження основного фото
        $mainPhotoPath = $request->file('main_photo')->store('accommodations', 'public');

        // Створення помешкання
        $accommodation = Accommodation::create([
            'title' => $request->title,
            'main_photo' => $mainPhotoPath
        ]);

        // Додавання додаткових фото
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPath = $photo->store('accommodations', 'public');
                AccommodationPhoto::create([
                    'accommodation_id' => $accommodation->id,
                    'photo_path' => $photoPath
                ]);
            }
        }
        return redirect()->route('accommodations.index')->with('success', 'Помешкання створено!');
    }
    public function showDetails($id)
{
    // Отримуємо інформацію про помешкання
    $accommodation = DB::table('accommodations')->find($id);

    // Отримуємо категорії і зручності
    $categories = DB::table('categories')
        ->join('category_amenity', 'categories.id', '=', 'category_amenity.category_id')
        ->join('amenities', 'category_amenity.amenity_id', '=', 'amenities.id')
        ->select('categories.name as category_name', 'amenities.name as amenity_name', 'amenities.icon_class')
        ->where('accommodation_id', $id)
        ->get();

    // Повертаємо в шаблон
    return view('details', compact('accommodation', 'categories'));
}
public function getMealsForAccommodation($id) {
    $accommodation = Accommodation::findOrFail($id);
    $meals = $accommodation->mealOptions()->get(['meal_options.name', 'accommodation_meal_option.price']);

    return response()->json($meals);
}
}

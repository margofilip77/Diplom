<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use Illuminate\Http\Request;
use App\Models\AccommodationPhoto;
use Illuminate\Support\Facades\DB;
use App\Models\Meal;
use App\Models\AmenityCategory;
use Illuminate\Support\Facades\Auth;
use App\Models\Amenity;

class AccommodationController extends Controller
{
    public function index()
    {
        $accommodations = Accommodation::with('reviews')->get();
        $regions = Accommodation::distinct()->pluck('region')->sort();
        $amenityCategories = AmenityCategory::with('amenities')->get();
        return view('accommodations.index', compact('accommodations', 'regions', 'amenityCategories'));
    }

    public function search(Request $request)
    {
        $query = Accommodation::query()->with(['reviews', 'amenities']);

        // Фільтр за регіоном (необов'язковий)
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        // Фільтр за селом (необов'язковий)
        if ($request->filled('settlement')) {
            $query->where('settlement', $request->settlement);
        }

        // Фільтр за датами (необов'язковий)
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->whereBetween('date', [$request->check_in, $request->check_out]);
            });
        } elseif ($request->filled('check_in')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->where('date', '>=', $request->check_in);
            });
        } elseif ($request->filled('check_out')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->where('date', '<=', $request->check_out);
            });
        }

        // Фільтр за кількістю гостей (необов'язковий)
        if ($request->filled('guests')) {
            $query->where('capacity', '>=', $request->guests);
        }

        // Фільтр за ціною (необов'язковий)
        if ($request->filled('minPrice')) {
            $query->where('price_per_night', '>=', $request->minPrice);
        }
        if ($request->filled('maxPrice')) {
            $query->where('price_per_night', '<=', $request->maxPrice);
        }

        // Фільтр за зручностями (помешкання має мати всі вибрані зручності)
        if ($request->filled('amenities')) {
            $amenities = array_map('intval', explode(',', $request->amenities));
            $query->whereHas('amenities', function ($q) use ($amenities) {
                $q->whereIn('amenities.id', $amenities);
            }, '>=', count($amenities));
        }

        // Сортування за рейтингом (використовуємо підзапит для середнього рейтингу)
        if ($request->filled('sort_rating')) {
            $sortDirection = $request->sort_rating === 'asc' ? 'ASC' : 'DESC';
            $query->select('accommodations.*')
                  ->selectSub(function ($subQuery) {
                      $subQuery->selectRaw('AVG(rating)')
                               ->from('reviews')
                               ->whereColumn('reviews.accommodation_id', 'accommodations.id');
                  }, 'average_rating')
                  ->orderByRaw('average_rating IS NULL, average_rating ' . $sortDirection);
        }

        $accommodations = $query->get();
        $regions = Accommodation::distinct()->pluck('region')->sort();
        $amenityCategories = AmenityCategory::with('amenities')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('accommodations.index', compact('accommodations', 'regions', 'amenityCategories'))->render(),
                'count' => $accommodations->count()
            ]);
        }

        return view('accommodations.index', compact('accommodations', 'regions', 'amenityCategories'));
    }

    public function show($id)
    {
        $accommodation = Accommodation::with(['amenities', 'city'])->findOrFail($id);
        $user = Auth::user();
        $cartItemCount = 0;
        if ($user && $user->cart && $user->cart->cartMeals) {
            $cartItemCount = $user->cart->cartMeals->count();
        }
        return view('accommodations.details', compact('accommodation', 'cartItemCount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'main_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $mainPhotoPath = $request->file('main_photo')->store('accommodations', 'public');
        $accommodation = Accommodation::create([
            'title' => $request->title,
            'main_photo' => $mainPhotoPath
        ]);
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
        $accommodation = DB::table('accommodations')->find($id);
        $categories = DB::table('categories')
            ->join('category_amenity', 'categories.id', '=', 'category_amenity.category_id')
            ->join('amenities', 'category_amenity.amenity_id', '=', 'amenities.id')
            ->select('categories.name as category_name', 'amenities.name as amenity_name', 'amenities.icon_class')
            ->where('accommodation_id', $id)
            ->get();
        return view('details', compact('accommodation', 'categories'));
    }

    public function getMealsForAccommodation($id)
    {
        $accommodation = Accommodation::findOrFail($id);
        $meals = $accommodation->mealOptions()->get(['meal_options.name', 'accommodation_meal_option.price']);
        return response()->json($meals);
    }

    public function getSettlementsByRegion(Request $request)
    {
        $region = $request->input('region');
        $settlements = Accommodation::where('region', $region)
            ->distinct()
            ->pluck('settlement')
            ->sort()
            ->values();
        return response()->json($settlements);
    }
}

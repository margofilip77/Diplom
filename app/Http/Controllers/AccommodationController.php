<?php

namespace App\Http\Controllers;

use App\Models\Accommodation;
use App\Models\Region; // Переконайтеся, що модель Region підключена
use Illuminate\Http\Request;
use App\Models\AccommodationPhoto;
use Illuminate\Support\Facades\DB;
use App\Models\Meal;
use App\Models\AmenityCategory;
use Illuminate\Support\Facades\Auth;
use App\Models\Amenity;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class AccommodationController extends Controller
{
    public function index()
    {
        $accommodations = Accommodation::with('reviews')
            ->where('is_available', 1)
            ->where('status', 'approved')
            ->get();
        $regions = Region::orderBy('id')->pluck('name');
        $amenityCategories = AmenityCategory::with('amenities')->get();
    
        // Отримуємо всі унікальні settlement для кожного region_id
        $regionSettlements = Accommodation::select('region_id', 'settlement')
            ->distinct('settlement')
            ->get()
            ->groupBy('region_id')
            ->map(function ($group) {
                return $group->pluck('settlement')->sort()->values()->toArray();
            })->toArray();
    
        return view('accommodations.index', compact('accommodations', 'regions', 'amenityCategories', 'regionSettlements'));
    }
    
    public function search(Request $request)
    {
        $query = Accommodation::query()
            ->with(['reviews', 'amenities', 'bookings'])
            ->where('is_available', 1)
            ->where('status', 'approved');
    
        if ($request->filled('region')) {
            $region = Region::where('name', $request->region)->first();
            if ($region) {
                $query->where('region_id', $region->id);
            }
        }
    
        if ($request->filled('settlement')) {
            $query->where('settlement', $request->settlement);
        }
    
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('checkin_date', '<=', $request->check_out)
                      ->where('checkout_date', '>=', $request->check_in);
                });
            });
        } elseif ($request->filled('check_in')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->where('checkin_date', '<=', $request->check_in)
                  ->where('checkout_date', '>=', $request->check_in);
            });
        } elseif ($request->filled('check_out')) {
            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->where('checkin_date', '<=', $request->check_out)
                  ->where('checkout_date', '>=', $request->check_out);
            });
        }
    
        // Фільтрація за точною кількістю осіб
        if ($request->filled('guests')) {
            $guests = (int)$request->guests;
            $query->where('capacity', '=', $guests); // Змінено на точне співпадіння
        }
    
        if ($request->filled('minPrice')) {
            $query->where('price_per_night', '>=', $request->minPrice);
        }
        if ($request->filled('maxPrice')) {
            $query->where('price_per_night', '<=', $request->maxPrice);
        }
    
        if ($request->filled('amenities')) {
            $amenities = array_map('intval', explode(',', $request->amenities));
            $query->whereHas('amenities', function ($q) use ($amenities) {
                $q->whereIn('amenities.id', $amenities);
            }, '>=', count($amenities));
        }
    
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
        $regions = Region::orderBy('id')->pluck('name');
        $amenityCategories = AmenityCategory::with('amenities')->get();
    
        // Отримуємо всі унікальні settlement для кожного region_id
        $regionSettlements = Accommodation::select('region_id', 'settlement')
            ->distinct('settlement')
            ->get()
            ->groupBy('region_id')
            ->map(function ($group) {
                return $group->pluck('settlement')->sort()->values()->toArray();
            })->toArray();
    
        if ($request->ajax()) {
            return response()->json([
                'html' => view('accommodations.index', compact('accommodations', 'regions', 'amenityCategories', 'regionSettlements'))->render(),
                'count' => $accommodations->count()
            ]);
        }
    
        return view('accommodations.index', compact('accommodations', 'regions', 'amenityCategories', 'regionSettlements'));
    }

    public function show($id)
    {
        $accommodation = Accommodation::with(['photos', 'mealOptions', 'amenities', 'reviews'])
            ->where('is_available', 1)
            ->where('status', 'approved')
            ->findOrFail($id);

        $bookings = Booking::where('accommodation_id', $accommodation->id)
            ->where('checkout_date', '>=', now())
            ->get(['checkin_date', 'checkout_date']);

        $bookedDates = $bookings->map(function ($booking) {
            return [
                'start' => $booking->checkin_date->format('Y-m-d'),
                'end' => $booking->checkout_date->format('Y-m-d'),
            ];
        })->toArray();

        Log::info('Booked dates for accommodation #' . $id . ': ', $bookedDates);

        return view('accommodations.details', compact('accommodation', 'bookedDates'));
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
        return redirect()->route('accommodations.')->with('success', 'Помешкання створено!');
    }

    public function showDetails($id)
    {
        $accommodation = DB::table('accommodations')
            ->where('is_available', 1)
            ->where('status', 'approved')
            ->find($id);
        if (!$accommodation) {
            abort(404, 'Помешкання не знайдено або недоступне.');
        }
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
        $accommodation = Accommodation::where('is_available', 1)
            ->where('status', 'approved')
            ->findOrFail($id);
        $meals = $accommodation->mealOptions()->get(['meal_options.name', 'accommodation_meal_option.price']);
        return response()->json($meals);
    }

    public function getSettlementsByRegion(Request $request)
    {
        $regionName = $request->input('region');
    
        if (!$regionName) {
            return response()->json(['error' => 'Регіон не вказано'], 400);
        }
    
        // Шукаємо регіон, ігноруючи регістр
        $region = Region::whereRaw('LOWER(name) = ?', [strtolower($regionName)])->first();
    
        if (!$region) {
            Log::warning('Регіон не знайдено в базі: ' . $regionName);
            return response()->json([]);
        }
    
        $settlements = Accommodation::where('region_id', $region->id)
            ->distinct()
            ->pluck('settlement')
            ->sort()
            ->values();
    
        return response()->json($settlements->toArray());
    }
}

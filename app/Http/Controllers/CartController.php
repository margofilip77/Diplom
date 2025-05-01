<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\MealOption;
use App\Models\CartMeal;
use Illuminate\Http\Request;
use App\Models\Accommodation;
use Illuminate\Support\Facades\Auth;
use App\Models\CartAccommodation;
use App\Models\CartAccommodationMealOption;
use Illuminate\Support\Facades\Session;
use App\Models\Package;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Booking;
use App\Models\BookingService;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        // Метод index не використовується для відображення кошика, повертаємо JSON
        if (Auth::check()) {
            $userId = Auth::id();
            $carts = Cart::where('user_id', $userId)
                ->with(['accommodations.mealOptions.mealOption'])
                ->get();
            return response()->json($carts);
        }

        $cartData = Session::get('cart', []);
        return response()->json($cartData);
    }

    public function addPackage(Request $request, $packageId)
    {
        if (!Session::has('cart')) {
            return response()->json([
                'success' => false,
                'error' => 'Спочатку оберіть помешкання.'
            ], 400);
        }

        $package = Package::findOrFail($packageId);
        $cartData = Session::get('cart', []);

        $matchingAccommodation = null;
        foreach ($cartData as $accommodationId => $data) {
            if (!$package->region_id || $package->region_id == ($data['region_id'] ?? null)) {
                $matchingAccommodation = $accommodationId;
                break;
            }
        }

        if (!$matchingAccommodation) {
            return response()->json([
                'success' => false,
                'error' => 'Немає помешкання в кошику, яке відповідає регіону цього пакета.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Пакет можна додати до кошика.',
            'accommodation_id' => $matchingAccommodation,
            'package' => [
                'id' => $package->id,
                'name' => $package->name,
                'price' => $package->calculatePrice(),
                'original_price' => $package->originalPrice(),
                'discount' => $package->discount,
            ]
        ]);
    }

    public function add(Request $request)
    {
        if (Auth::check() && Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Ваш обліковий запис заблоковано. Ви не можете додавати помешкання до кошика.');
        }
        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::where('user_id', $userId)->first();
            if (!$cart) {
                $cart = Cart::create(['user_id' => $userId]);
            }

            $accommodation = Accommodation::with('mealOptions')->findOrFail($request->accommodation_id);
            $accommodationPhoto = $accommodation->photos()->first()->photo_path ?? null;

            if (!$accommodationPhoto) {
                return response()->json(['error' => 'Фото не знайдено для цього помешкання'], 404);
            }

            $totalPrice = $request->input('total_price', $accommodation->price_per_night);

            $cartAccommodation = $cart->accommodations()->create([
                'accommodation_id' => $accommodation->id,
                'checkin_date' => $request->checkin_date,
                'checkout_date' => $request->checkout_date,
                'guests_count' => json_encode($request->guests_count),
                'accommodation_photo' => $accommodationPhoto,
                'price' => $accommodation->price_per_night,
            ]);

            $mealOptionsData = [];
            if ($request->has('meal_options')) {
                foreach ($request->meal_options as $mealOption) {
                    $mealOptionModel = $accommodation->mealOptions->where('id', $mealOption['meal_option_id'])->first();
                    $mealPrice = $mealOptionModel ? $mealOptionModel->pivot->price : 0;

                    CartAccommodationMealOption::create([
                        'cart_accommodation_id' => $cartAccommodation->id,
                        'meal_option_id' => $mealOption['meal_option_id'],
                        'guests_count' => $mealOption['guests_count'],
                        'price' => $mealPrice,
                    ]);

                    $mealOptionsData[] = [
                        'meal_option_id' => $mealOption['meal_option_id'],
                        'guests_count' => $mealOption['guests_count'],
                        'price' => $mealPrice,
                    ];
                }
            }

            $cartData = Session::get('cart', []);
            $cartData[$cartAccommodation->id] = [
                'accommodation_id' => $accommodation->id,
                'checkin_date' => $request->checkin_date,
                'checkout_date' => $request->checkout_date,
                'guests_count' => $request->guests_count,
                'accommodation_photo' => $accommodationPhoto,
                'price' => $accommodation->price_per_night,
                'total_price' => $totalPrice,
                'meal_options' => $mealOptionsData,
                'region_id' => $accommodation->city->region_id ?? $accommodation->region_id,
            ];
            Session::put('cart', $cartData);

            $cartItemsCount = $cart->accommodations()->count();
        } else {
            $cartData = Session::get('cart', []);

            $cartItemId = time();

            $accommodation = Accommodation::with('mealOptions')->findOrFail($request->accommodation_id);
            $accommodationPhoto = $accommodation->photos()->first()->photo_path ?? null;

            if (!$accommodationPhoto) {
                return response()->json(['error' => 'Фото не знайдено для цього помешкання'], 404);
            }

            $totalPrice = $request->input('total_price', $accommodation->price_per_night);

            $mealOptionsData = [];
            if ($request->has('meal_options')) {
                foreach ($request->meal_options as $mealOption) {
                    $mealOptionModel = $accommodation->mealOptions->where('id', $mealOption['meal_option_id'])->first();
                    $mealPrice = $mealOptionModel ? $mealOptionModel->pivot->price : 0;

                    $mealOptionsData[] = [
                        'meal_option_id' => $mealOption['meal_option_id'],
                        'guests_count' => $mealOption['guests_count'],
                        'price' => $mealPrice,
                    ];
                }
            }

            $cartData[$cartItemId] = [
                'accommodation_id' => $accommodation->id,
                'checkin_date' => $request->checkin_date,
                'checkout_date' => $request->checkout_date,
                'guests_count' => $request->guests_count,
                'accommodation_photo' => $accommodationPhoto,
                'price' => $accommodation->price_per_night,
                'total_price' => $totalPrice,
                'meal_options' => $mealOptionsData,
                'region_id' => $accommodation->city->region_id ?? $accommodation->region_id,
            ];

            Session::put('cart', $cartData);
            $cartItemsCount = count($cartData);
        }

        return response()->json([
            'success' => 'Помешкання та харчування додано до кошика',
            'cart_items_count' => $cartItemsCount
        ]);
    }

    public function showCart()
    {
        $cartItemsCount = 0;
        $total = 0;
        $cartSessionData = Session::get('cart', []);

        // Remove duplicates from session data
        $uniqueCartSessionData = [];
        foreach ($cartSessionData as $item) {
            $key = $item['accommodation_id'] . '-' . $item['checkin_date'] . '-' . $item['checkout_date'];
            if (!isset($uniqueCartSessionData[$key])) {
                $uniqueCartSessionData[$key] = $item;
            }
        }
        $cartSessionData = array_values($uniqueCartSessionData);
        Session::put('cart', $cartSessionData);

        // Load all packages
        $packages = Package::with('services')->get();

        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::with(['accommodations.accommodation.city.region', 'accommodations.mealOptions.mealOption'])
                ->where('user_id', $userId)
                ->first();

            if (!empty($cartSessionData)) {
                if (!$cart) {
                    $cart = Cart::create(['user_id' => $userId]);
                }

                foreach ($cartSessionData as $item) {
                    $exists = $cart->accommodations->contains('accommodation_id', $item['accommodation_id']);
                    if (!$exists) {
                        $cartAccommodation = new CartAccommodation([
                            'cart_id' => $cart->id,
                            'accommodation_id' => $item['accommodation_id'],
                            'checkin_date' => $item['checkin_date'],
                            'checkout_date' => $item['checkout_date'],
                            'guests_count' => json_encode($item['guests_count']),
                            'accommodation_photo' => $item['accommodation_photo'],
                            'price' => $item['price'],
                        ]);
                        $cartAccommodation->save();

                        if (!empty($item['meal_options'])) {
                            foreach ($item['meal_options'] as $mealOption) {
                                CartAccommodationMealOption::create([
                                    'cart_accommodation_id' => $cartAccommodation->id,
                                    'meal_option_id' => $mealOption['meal_option_id'],
                                    'guests_count' => $mealOption['guests_count'],
                                    'price' => $mealOption['price'] ?? 0,
                                ]);
                            }
                        }
                    }
                }
                Session::forget('cart');
            }

            $cart = Cart::with(['accommodations.accommodation.city.region', 'accommodations.mealOptions.mealOption'])
                ->where('user_id', $userId)
                ->first();

            $cartItemsCount = $cart ? $cart->accommodations()->count() : 0;

            if ($cart && $cart->accommodations) {
                foreach ($cart->accommodations as $item) {
                    $checkinDate = \Carbon\Carbon::parse($item->checkin_date);
                    $checkoutDate = \Carbon\Carbon::parse($item->checkout_date);
                    $nights = abs($checkoutDate->diffInDays($checkinDate));

                    $accommodationPrice = $item->price * $nights;
                    $mealTotal = $item->mealOptions->sum(function ($cartMealOption) {
                        return ($cartMealOption->price ?? 0) * $cartMealOption->guests_count;
                    });

                    $itemTotal = $accommodationPrice + $mealTotal;
                    $item->itemTotal = $itemTotal;
                    $total += $itemTotal;

                    // Use region_id for filtering services
                    $regionId = $item->accommodation->city->region_id ?? $item->accommodation->region_id;
                    $services = Service::with('category')
                        ->where('region_id', $regionId)
                        ->get();

                    // Remove distance calculation since city_id and coordinates are not available
                    foreach ($services as $service) {
                        $service->distance = null; // Set distance to null as we don't have city coordinates
                    }

                    $item->availableServices = $services->groupBy('category_id');
                    $item->serviceCategories = ServiceCategory::all();
                }
            }
        } else {
            $cartItems = [];
            $cartItemsCount = count($cartSessionData);

            foreach ($cartSessionData as $key => $item) {
                $accommodation = Accommodation::with(['city.region'])->find($item['accommodation_id']);
                if ($accommodation) {
                    $checkinDate = \Carbon\Carbon::parse($item['checkin_date']);
                    $checkoutDate = \Carbon\Carbon::parse($item['checkout_date']);
                    $nights = abs($checkoutDate->diffInDays($checkinDate));

                    $accommodationPrice = $item['price'] * $nights;

                    $mealTotal = 0;
                    $mealOptions = collect();
                    if (!empty($item['meal_options'])) {
                        foreach ($item['meal_options'] as $mealOption) {
                            $meal = \App\Models\MealOption::find($mealOption['meal_option_id']);
                            if ($meal) {
                                $mealPrice = $mealOption['price'] ?? 0;
                                $mealTotal += $mealPrice * $mealOption['guests_count'];
                                $mealOptions->push((object) [
                                    'mealOption' => $meal,
                                    'guests_count' => $mealOption['guests_count'],
                                    'price' => $mealPrice,
                                ]);
                            }
                        }
                    }

                    $itemTotal = $accommodationPrice + $mealTotal;
                    $total += $itemTotal;

                    // Use region_id for filtering services
                    $regionId = $accommodation->city->region_id ?? $accommodation->region_id;
                    $services = Service::with('category')
                        ->where('region_id', $regionId)
                        ->get();

                    foreach ($services as $service) {
                        $service->distance = null;
                    }

                    $cartItems[$key] = (object) [
                        'accommodation' => $accommodation,
                        'checkin_date' => $item['checkin_date'],
                        'checkout_date' => $item['checkout_date'],
                        'guests_count' => $item['guests_count'],
                        'accommodation_photo' => $item['accommodation_photo'],
                        'price' => $item['price'],
                        'availableServices' => $services->groupBy('category_id'),
                        'serviceCategories' => ServiceCategory::all(),
                        'mealOptions' => $mealOptions,
                        'itemTotal' => $itemTotal,
                    ];
                }
            }

            $cart = (object) ['accommodations' => collect($cartItems)];
        }

        $total = max(0, $total);

        return view('cart.index', compact('cart', 'cartSessionData', 'cartItemsCount', 'total', 'packages'));
    }
    public function checkout()
    {
        $cartItemsCount = 0;
        $total = 0;
        $cartSessionData = Session::get('cart', []);
        $cartData = json_decode(request()->input('cartData'), true) ?? [];
        $packages = Package::with('services')->get();

        // Зберігаємо cartData у сесію
        Session::put('cartData', $cartData);

        logger()->info('Cart Data in checkout method:', $cartData);

        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::with(['accommodations.accommodation.city.region', 'accommodations.mealOptions.mealOption'])
                ->where('user_id', $userId)
                ->first();

            if (!empty($cartSessionData)) {
                if (!$cart) {
                    $cart = Cart::create(['user_id' => $userId]);
                }

                foreach ($cartSessionData as $item) {
                    $exists = $cart->accommodations->contains('accommodation_id', $item['accommodation_id']);
                    if (!$exists) {
                        $cartAccommodation = new CartAccommodation([
                            'cart_id' => $cart->id,
                            'accommodation_id' => $item['accommodation_id'],
                            'checkin_date' => $item['checkin_date'],
                            'checkout_date' => $item['checkout_date'],
                            'guests_count' => json_encode($item['guests_count']),
                            'accommodation_photo' => $item['accommodation_photo'],
                            'price' => $item['price'],
                        ]);
                        $cartAccommodation->save();

                        if (!empty($item['meal_options'])) {
                            foreach ($item['meal_options'] as $mealOption) {
                                CartAccommodationMealOption::create([
                                    'cart_accommodation_id' => $cartAccommodation->id,
                                    'meal_option_id' => $mealOption['meal_option_id'],
                                    'guests_count' => $mealOption['guests_count'],
                                    'price' => $mealOption['price'] ?? 0,
                                ]);
                            }
                        }
                    }
                }
                Session::forget('cart');
            }

            $cart = Cart::with(['accommodations.accommodation.city.region', 'accommodations.mealOptions.mealOption'])
                ->where('user_id', $userId)
                ->first();

            $cartItemsCount = $cart ? $cart->accommodations()->count() : 0;

            if ($cart && $cart->accommodations) {
                foreach ($cart->accommodations as $item) {
                    $checkinDate = \Carbon\Carbon::parse($item->checkin_date);
                    $checkoutDate = \Carbon\Carbon::parse($item->checkout_date);
                    $nights = max(1, $checkinDate->diffInDays($checkoutDate, false));

                    $accommodationPrice = max(0, $item->price * $nights);
                    $mealTotal = max(0, $item->mealOptions->sum(function ($cartMealOption) {
                        return ($cartMealOption->price ?? 0) * max(1, $cartMealOption->guests_count);
                    }));

                    $itemId = $item->id;
                    $serviceTotal = max(0, $cartData[$itemId]['service_total'] ?? 0);
                    $packageTotal = max(0, $cartData[$itemId]['package_total'] ?? 0);
                    $itemTotal = $accommodationPrice + $mealTotal + $serviceTotal + $packageTotal;
                    $item->itemTotal = $itemTotal;
                    $total += $itemTotal;

                    logger()->info("Accommodation {$itemId}: Nights = {$nights}, Accommodation Price = {$accommodationPrice}, Meal Total = {$mealTotal}, Service Total = {$serviceTotal}, Package Total = {$packageTotal}, Item Total = {$itemTotal}");

                    $regionId = $item->accommodation->city->region_id ?? $item->accommodation->region_id;
                    $services = Service::with('category')
                        ->where('region_id', $regionId)
                        ->get();

                    foreach ($services as $service) {
                        $service->distance = null;
                    }

                    $item->availableServices = $services->groupBy('category_id');
                    $item->serviceCategories = ServiceCategory::all();
                    $item->selectedServices = $cartData[$itemId]['services'] ?? []; // Додаємо обрані послуги
                    $item->selectedPackages = $cartData[$itemId]['packages'] ?? []; // Додаємо обрані пакети
                }
            }
        } else {
            $cartItems = [];
            $cartItemsCount = count($cartSessionData);

            foreach ($cartSessionData as $key => $item) {
                $accommodation = Accommodation::with(['city.region'])->find($item['accommodation_id']);
                if ($accommodation) {
                    $checkinDate = \Carbon\Carbon::parse($item['checkin_date']);
                    $checkoutDate = \Carbon\Carbon::parse($item['checkout_date']);
                    $nights = max(1, $checkinDate->diffInDays($checkoutDate, false));

                    $accommodationPrice = max(0, $item['price'] * $nights);
                    $mealTotal = 0;
                    $mealOptions = collect();
                    if (!empty($item['meal_options'])) {
                        foreach ($item['meal_options'] as $mealOption) {
                            $meal = \App\Models\MealOption::find($mealOption['meal_option_id']);
                            if ($meal) {
                                $mealPrice = max(0, $mealOption['price'] ?? 0);
                                $mealTotal += $mealPrice * max(1, $mealOption['guests_count']);
                                $mealOptions->push((object) [
                                    'mealOption' => $meal,
                                    'guests_count' => $mealOption['guests_count'],
                                    'price' => $mealPrice,
                                ]);
                            }
                        }
                    }

                    $serviceTotal = max(0, $cartData[$key]['service_total'] ?? 0);
                    $packageTotal = max(0, $cartData[$key]['package_total'] ?? 0);
                    $itemTotal = $accommodationPrice + $mealTotal + $serviceTotal + $packageTotal;
                    $total += $itemTotal;

                    logger()->info("Accommodation {$key} (Guest): Nights = {$nights}, Accommodation Price = {$accommodationPrice}, Meal Total = {$mealTotal}, Service Total = {$serviceTotal}, Package Total = {$packageTotal}, Item Total = {$itemTotal}");

                    $regionId = $accommodation->city->region_id ?? $accommodation->region_id;
                    $services = Service::with('category')
                        ->where('region_id', $regionId)
                        ->get();

                    foreach ($services as $service) {
                        $service->distance = null;
                    }

                    $cartItems[$key] = (object) [
                        'accommodation' => $accommodation,
                        'checkin_date' => $item['checkin_date'],
                        'checkout_date' => $item['checkout_date'],
                        'guests_count' => $item['guests_count'],
                        'accommodation_photo' => $item['accommodation_photo'],
                        'price' => $item['price'],
                        'availableServices' => $services->groupBy('category_id'),
                        'serviceCategories' => ServiceCategory::all(),
                        'mealOptions' => $mealOptions,
                        'itemTotal' => $itemTotal,
                        'selectedServices' => $cartData[$key]['services'] ?? [], // Додаємо обрані послуги
                        'selectedPackages' => $cartData[$key]['packages'] ?? [], // Додаємо обрані пакети
                    ];
                }
            }

            $cart = (object) ['accommodations' => collect($cartItems)];
        }

        $total = max(0, $total);

        return view('cart.checkout', compact('cart', 'cartSessionData', 'cartItemsCount', 'total', 'packages', 'cartData'));
    }

    public function storeCheckout(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'comments' => 'nullable|string',
        ]);

        $cartData = json_decode($request->input('cartData'), true) ?? [];
        $cartSessionData = Session::get('cart', []);

        logger()->info('Cart Data on Checkout:', $cartData);

        $grandTotal = 0;

        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::with(['accommodations.accommodation.city.region', 'accommodations.mealOptions.mealOption'])
                ->where('user_id', $userId)
                ->first();

            if (!$cart || $cart->accommodations->isEmpty()) {
                return redirect()->route('cart.show')->with('error', 'Ваш кошик порожній.');
            }

            foreach ($cart->accommodations as $item) {
                $checkinDate = \Carbon\Carbon::parse($item->checkin_date);
                $checkoutDate = \Carbon\Carbon::parse($item->checkout_date);
                $nights = max(1, abs($checkoutDate->diffInDays($checkinDate)));

                $accommodationPrice = max(0, $item->price) * $nights;
                $mealTotal = max(0, $item->mealOptions->sum(function ($cartMealOption) {
                    return max(0, ($cartMealOption->price ?? 0)) * max(1, $cartMealOption->guests_count);
                }));

                $itemId = $item->id;
                $serviceTotal = max(0, $cartData[$itemId]['service_total'] ?? 0);
                $packageTotal = max(0, $cartData[$itemId]['package_total'] ?? 0);
                $itemTotal = $accommodationPrice + $mealTotal + $serviceTotal + $packageTotal;
                $grandTotal += $itemTotal;

                logger()->info("Booking for Item {$itemId}: Nights = {$nights}, Accommodation Price = {$accommodationPrice}, Meal Total = {$mealTotal}, Service Total = {$serviceTotal}, Package Total = {$packageTotal}, Item Total = {$itemTotal}");

                $booking = Booking::create([
                    'user_id' => $userId,
                    'accommodation_id' => $item->accommodation_id,
                    'checkin_date' => $item->checkin_date,
                    'checkout_date' => $item->checkout_date,
                    'guests_count' => $item->guests_count,
                    'total_price' => $itemTotal,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'comments' => $request->comments,
                ]);

                // Save individual services
                if (isset($cartData[$itemId]['services']) && is_array($cartData[$itemId]['services'])) {
                    foreach ($cartData[$itemId]['services'] as $service) {
                        if (isset($service['id'])) {
                            BookingService::create([
                                'booking_id' => $booking->id,
                                'service_id' => $service['id'],
                            ]);
                        }
                    }
                }

                // Save package services
                if (isset($cartData[$itemId]['packages']) && is_array($cartData[$itemId]['packages'])) {
                    foreach ($cartData[$itemId]['packages'] as $packageData) {
                        $package = Package::find($packageData['id']);
                        if ($package) {
                            foreach ($package->services as $service) {
                                BookingService::create([
                                    'booking_id' => $booking->id,
                                    'service_id' => $service->id,
                                ]);
                            }
                        }
                    }
                }
            }

            $cart->accommodations()->delete();
            $cart->delete();
        } else {
            if (empty($cartSessionData)) {
                return redirect()->route('cart.show')->with('error', 'Ваш кошик порожній.');
            }

            foreach ($cartSessionData as $key => $item) {
                $accommodation = Accommodation::find($item['accommodation_id']);
                if ($accommodation) {
                    $checkinDate = \Carbon\Carbon::parse($item['checkin_date']);
                    $checkoutDate = \Carbon\Carbon::parse($item['checkout_date']);
                    $nights = max(1, abs($checkoutDate->diffInDays($checkinDate)));

                    $accommodationPrice = max(0, $item['price']) * $nights;
                    $mealTotal = 0;
                    if (!empty($item['meal_options'])) {
                        foreach ($item['meal_options'] as $mealOption) {
                            $mealPrice = max(0, $mealOption['price'] ?? 0);
                            $mealTotal += $mealPrice * max(1, $mealOption['guests_count']);
                        }
                    }

                    $serviceTotal = max(0, $cartData[$key]['service_total'] ?? 0);
                    $packageTotal = max(0, $cartData[$key]['package_total'] ?? 0);
                    $itemTotal = $accommodationPrice + $mealTotal + $serviceTotal + $packageTotal;
                    $grandTotal += $itemTotal;

                    logger()->info("Booking for Item {$key} (Guest): Nights = {$nights}, Accommodation Price = {$accommodationPrice}, Meal Total = {$mealTotal}, Service Total = {$serviceTotal}, Package Total = {$packageTotal}, Item Total = {$itemTotal}");

                    $booking = Booking::create([
                        'user_id' => null,
                        'accommodation_id' => $item['accommodation_id'],
                        'checkin_date' => $item['checkin_date'],
                        'checkout_date' => $item['checkout_date'],
                        'guests_count' => json_encode($item['guests_count']),
                        'total_price' => $itemTotal,
                        'name' => $request->name,
                        'email' => $request->email,
                        'phone' => $request->phone,
                        'comments' => $request->comments,
                    ]);

                    // Save individual services
                    if (isset($cartData[$key]['services']) && is_array($cartData[$key]['services'])) {
                        foreach ($cartData[$key]['services'] as $service) {
                            if (isset($service['id'])) {
                                BookingService::create([
                                    'booking_id' => $booking->id,
                                    'service_id' => $service['id'],
                                ]);
                            }
                        }
                    }

                    // Save package services
                    if (isset($cartData[$key]['packages']) && is_array($cartData[$key]['packages'])) {
                        foreach ($cartData[$key]['packages'] as $packageData) {
                            $package = Package::find($packageData['id']);
                            if ($package) {
                                foreach ($package->services as $service) {
                                    BookingService::create([
                                        'booking_id' => $booking->id,
                                        'service_id' => $service->id,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        // Clear session and localStorage data
        Session::forget('cart');
        Session::forget('cartData');

        return redirect()->route('cart.show')->with('success', 'Замовлення успішно оформлено!');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function remove(Request $request, $id)
    {
        if (Auth::check() && Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Ваш обліковий запис заблоковано. Ви не можете видаляти помешкання з кошика.');
        }
        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::where('user_id', $userId)->first();

            if ($cart) {
                $cartAccommodation = $cart->accommodations()->where('id', $id)->first();
                if ($cartAccommodation) {
                    $cartAccommodation->mealOptions()->delete();
                    $cartAccommodation->delete();

                    $cartData = Session::get('cart', []);
                    if (isset($cartData[$id])) {
                        unset($cartData[$id]);
                        Session::put('cart', $cartData);
                    }

                    return redirect()->route('cart.show')->with('success', 'Елемент успішно видалено з кошика');
                }
                logger()->warning("CartAccommodation with ID {$id} not found for user {$userId}");
                return redirect()->route('cart.show')->with('error', 'Елемент не знайдено в кошику');
            }
            logger()->warning("Cart not found for user {$userId}");
            return redirect()->route('cart.show')->with('error', 'Кошик не знайдено');
        } else {
            $cartData = Session::get('cart', []);
            if (isset($cartData[$id])) {
                unset($cartData[$id]);
                Session::put('cart', $cartData);

                return redirect()->route('cart.show')->with('success', 'Елемент успішно видалено з кошика');
            }
            logger()->warning("Cart item with ID {$id} not found in session");
            return redirect()->route('cart.show')->with('error', 'Елемент не знайдено в кошику');
        }
    }

    public function show($cartId)
    {
        $cart = Cart::with(['accommodations.mealOptions.mealOption'])->find($cartId);

        if (!$cart) {
            return view('cart.empty');
        }

        $guestsCount = json_decode($cart->accommodations->first()->guests_count, true);

        $totalPrice = $cart->accommodations->sum(function ($accommodation) {
            $mealTotal = $accommodation->mealOptions->sum(function ($cartMealOption) {
                return $cartMealOption->mealOption->price * $cartMealOption->guests_count;
            });
            $checkinDate = \Carbon\Carbon::parse($accommodation->checkin_date);
            $checkoutDate = \Carbon\Carbon::parse($accommodation->checkout_date);
            $nights = $checkoutDate->diffInDays($checkinDate);
            return $mealTotal + ($accommodation->price * $nights);
        });

        $packages = Package::with('services')->get();

        return view('cart.index', [
            'cart' => $cart,
            'totalPrice' => $totalPrice,
            'guestsCount' => $guestsCount,
            'packages' => $packages
        ]);
    }

    public function getCart(Request $request)
    {
        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::where('user_id', $userId)
                ->with(['accommodations.mealOptions.mealOption'])
                ->get();
            return response()->json($cart);
        }

        $cartData = Session::get('cart', []);
        return response()->json($cartData);
    }

    public function updateCart(Request $request, $cartId)
    {
        if (Auth::check()) {
            $cartAccommodation = CartAccommodation::find($cartId);

            if (!$cartAccommodation) {
                return redirect()->route('cart.index')->with('error', 'Кошик не знайдений');
            }

            $cartAccommodation->mealOptions()->delete();

            if ($request->has('meal_option')) {
                foreach ($request->meal_option as $mealId => $guestsCount) {
                    if ($guestsCount > 0) {
                        $mealOption = MealOption::find($mealId);
                        if ($mealOption) {
                            CartAccommodationMealOption::create([
                                'cart_accommodation_id' => $cartAccommodation->id,
                                'meal_option_id' => $mealId,
                                'guests_count' => $guestsCount,
                            ]);
                        }
                    }
                }
            }

            return redirect()->route('cart.index')->with('success', 'Кошик оновлено');
        }

        $cartData = Session::get('cart', []);
        if (isset($cartData[$cartId]) && $request->has('meal_option')) {
            $cartData[$cartId]['meal_options'] = [];
            foreach ($request->meal_option as $mealId => $guestsCount) {
                if ($guestsCount > 0) {
                    $mealOption = MealOption::find($mealId);
                    if ($mealOption) {
                        $cartData[$cartId]['meal_options'][] = [
                            'meal_option_id' => $mealId,
                            'guests_count' => $guestsCount,
                        ];
                    }
                }
            }
            Session::put('cart', $cartData);
            return redirect()->route('cart.index')->with('success', 'Кошик оновлено');
        }

        return redirect()->route('cart.index')->with('error', 'Кошик не знайдений');
    }
}

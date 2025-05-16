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
use Illuminate\Support\Str;
use App\Models\BookingPackage;

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
        try {
            Log::info('Starting add method', [
                'request_data' => $request->all(),
                'user_id' => Auth::id() ?? 'Guest',
                'timestamp' => now()->toDateTimeString(),
            ]);

            if (Auth::check() && Auth::user()->is_blocked) {
                Log::warning('User is blocked', ['user_id' => Auth::id()]);
                return redirect()->route('home')->with('error', 'Ваш обліковий запис заблоковано.');
            }

            $accommodation = Accommodation::with('mealOptions')->findOrFail($request->accommodation_id);
            $accommodationPhoto = $accommodation->photos()->first()->photo_path ?? null;

            if (!$accommodationPhoto) {
                Log::error('Accommodation photo not found', ['accommodation_id' => $request->accommodation_id]);
                return response()->json(['error' => 'Фото не знайдено'], 404);
            }

            $totalPrice = $request->input('total_price', $accommodation->price_per_night);
            $checkinDate = $request->checkin_date;
            $checkoutDate = $request->checkout_date;
            $guestsCount = $request->guests_count;

            // Нормалізація guests_count
            $normalizedGuestsCount = $guestsCount;
            ksort($normalizedGuestsCount);
            $guestsCountJson = json_encode($normalizedGuestsCount);

            // Унікальний ключ для перевірки дублювання
            $uniqueKey = $accommodation->id . '-' . $checkinDate . '-' . $checkoutDate . '-' . $guestsCountJson;

            if (Auth::check()) {
                $userId = Auth::id();
                $cart = Cart::where('user_id', $userId)->first();

                if (!$cart) {
                    $cart = Cart::create(['user_id' => $userId]);
                    Log::info('Created new cart', ['user_id' => $userId, 'cart_id' => $cart->id]);
                }

                // Детальна перевірка на дублювання
                $existingAccommodation = $cart->accommodations()
                    ->where('accommodation_id', $accommodation->id)
                    ->where('checkin_date', $checkinDate)
                    ->where('checkout_date', $checkoutDate)
                    ->where('guests_count', $guestsCountJson)
                    ->first();

                if ($existingAccommodation) {
                    Log::warning('Duplicate accommodation found in DB', [
                        'user_id' => $userId,
                        'accommodation_id' => $accommodation->id,
                        'checkin_date' => $checkinDate,
                        'checkout_date' => $checkoutDate,
                        'guests_count' => $guestsCount,
                        'existing_cart_accommodation_id' => $existingAccommodation->id,
                        'existing_data' => $existingAccommodation->toArray(),
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Це помешкання вже є у вашому кошику.'
                    ], 400);
                }

                // Додаємо до бази даних
                $cartAccommodation = $cart->accommodations()->create([
                    'accommodation_id' => $accommodation->id,
                    'checkin_date' => $checkinDate,
                    'checkout_date' => $checkoutDate,
                    'guests_count' => $guestsCountJson,
                    'accommodation_photo' => $accommodationPhoto,
                    'price' => $accommodation->price_per_night,
                ]);

                Log::info('Added accommodation to cart', [
                    'user_id' => $userId,
                    'cart_accommodation_id' => $cartAccommodation->id,
                    'accommodation_id' => $accommodation->id,
                ]);

                // Додаємо meal_options
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
                    }
                }

                $cartItemsCount = $cart->accommodations()->count();
            } else {
                // Генеруємо guest_id, якщо його ще немає
                if (!Session::has('guest_id')) {
                    $guestId = Str::uuid()->toString();
                    Session::put('guest_id', $guestId);
                    Log::info('Generated new guest_id for unregistered user', ['guest_id' => $guestId]);
                } else {
                    $guestId = Session::get('guest_id');
                    Log::info('Using existing guest_id for unregistered user', ['guest_id' => $guestId]);
                }

                // Отримуємо кошик для цього guest_id
                $cartData = Session::get('cart', []);
                $guestCart = $cartData[$guestId] ?? [];

                // Перевірка на дублювання в межах кошика цього guest_id
                if (isset($guestCart[$uniqueKey])) {
                    Log::warning('Duplicate accommodation in session for guest', [
                        'guest_id' => $guestId,
                        'unique_key' => $uniqueKey,
                        'accommodation_id' => $accommodation->id,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Це помешкання вже є у вашому кошику.'
                    ], 400);
                }

                // Додаємо до кошика для цього guest_id
                $guestCart[$uniqueKey] = [
                    'accommodation_id' => $accommodation->id,
                    'checkin_date' => $checkinDate,
                    'checkout_date' => $checkoutDate,
                    'guests_count' => $normalizedGuestsCount,
                    'accommodation_photo' => $accommodationPhoto,
                    'price' => $accommodation->price_per_night,
                    'total_price' => $totalPrice,
                    'meal_options' => [],
                    'region_id' => $accommodation->city->region_id ?? $accommodation->region_id,
                ];

                if ($request->has('meal_options')) {
                    foreach ($request->meal_options as $mealOption) {
                        $mealOptionModel = $accommodation->mealOptions->where('id', $mealOption['meal_option_id'])->first();
                        $mealPrice = $mealOptionModel ? $mealOptionModel->pivot->price : 0;

                        $guestCart[$uniqueKey]['meal_options'][] = [
                            'meal_option_id' => $mealOption['meal_option_id'],
                            'guests_count' => $mealOption['guests_count'],
                            'price' => $mealPrice,
                        ];
                    }
                }

                // Зберігаємо кошик для цього guest_id
                $cartData[$guestId] = $guestCart;
                Session::put('cart', $cartData);
                $cartItemsCount = count($guestCart);
                Log::info('Added to session cart for guest', [
                    'guest_id' => $guestId,
                    'cart_data' => $guestCart,
                ]);
            }

            return response()->json([
                'success' => 'Помешкання додано до кошика',
                'cart_items_count' => $cartItemsCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error in add method', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Сталася помилка при додаванні до кошика.',
            ], 500);
        }
    }

    public function showCart()
    {
        Log::info('Starting showCart method', ['user_id' => Auth::id() ?? 'Guest']);

        $cartItemsCount = 0;
        $total = 0;
        $cartSessionData = [];

        // Для незареєстрованих користувачів отримуємо кошик за guest_id
        if (!Auth::check()) {
            $guestId = Session::get('guest_id');
            $allCartData = Session::get('cart', []);
            $cartSessionData = $guestId ? ($allCartData[$guestId] ?? []) : [];
            Log::info('Session cart data before deduplication', [
                'guest_id' => $guestId,
                'cart_session_data' => $cartSessionData,
            ]);

            $uniqueCartSessionData = [];
            foreach ($cartSessionData as $item) {
                $guestsCount = $item['guests_count'] ?? [];
                ksort($guestsCount);
                $uniqueKey = $item['accommodation_id'] . '-' . $item['checkin_date'] . '-' . $item['checkout_date'] . '-' . json_encode($guestsCount);
                if (!isset($uniqueCartSessionData[$uniqueKey])) {
                    $uniqueCartSessionData[$uniqueKey] = $item;
                } else {
                    Log::warning('Duplicate found in session cart data, skipping', ['unique_key' => $uniqueKey, 'item' => $item]);
                }
            }
            $cartSessionData = array_values($uniqueCartSessionData);
            if ($guestId) {
                $allCartData[$guestId] = $cartSessionData;
                Session::put('cart', $allCartData);
            }
            Log::info('Session cart data after deduplication', [
                'guest_id' => $guestId,
                'cart_session_data' => $cartSessionData,
            ]);
        }

        $packages = Package::with('services')->get();

        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::with(['accommodations.accommodation.city.region', 'accommodations.mealOptions.mealOption'])
                ->where('user_id', $userId)
                ->first();
            Log::info('Cart loaded from DB', ['user_id' => $userId, 'cart_id' => $cart ? $cart->id : null]);

            if (!empty($cartSessionData) && $cart) {
                foreach ($cartSessionData as $item) {
                    $guestsCount = $item['guests_count'] ?? [];
                    ksort($guestsCount);
                    $guestsCountJson = json_encode($guestsCount);

                    $exists = $cart->accommodations()
                        ->where('accommodation_id', $item['accommodation_id'])
                        ->where('checkin_date', $item['checkin_date'])
                        ->where('checkout_date', $item['checkout_date'])
                        ->where('guests_count', $guestsCountJson)
                        ->exists();

                    if ($exists) {
                        Log::info('Session item already exists in DB, skipping sync', [
                            'accommodation_id' => $item['accommodation_id'],
                            'checkin_date' => $item['checkin_date'],
                            'checkout_date' => $item['checkout_date'],
                            'guests_count' => $guestsCount,
                        ]);
                        continue;
                    }

                    $cartAccommodation = new CartAccommodation([
                        'cart_id' => $cart->id,
                        'accommodation_id' => $item['accommodation_id'],
                        'checkin_date' => $item['checkin_date'],
                        'checkout_date' => $item['checkout_date'],
                        'guests_count' => $guestsCountJson,
                        'accommodation_photo' => $item['accommodation_photo'],
                        'price' => $item['price'],
                    ]);
                    $cartAccommodation->save();

                    Log::info('Synced new session item to DB cart', [
                        'cart_accommodation_id' => $cartAccommodation->id,
                        'accommodation_id' => $item['accommodation_id'],
                        'checkin_date' => $item['checkin_date'],
                        'checkout_date' => $item['checkout_date'],
                        'guests_count' => $guestsCount,
                    ]);

                    if (!empty($item['meal_options'])) {
                        foreach ($item['meal_options'] as $mealOption) {
                            CartAccommodationMealOption::create([
                                'cart_accommodation_id' => $cartAccommodation->id,
                                'meal_option_id' => $mealOption['meal_option_id'],
                                'guests_count' => $mealOption['guests_count'],
                                'price' => $mealOption['price'] ?? 0,
                            ]);
                        }
                        Log::info('Synced meal options to DB', [
                            'cart_accommodation_id' => $cartAccommodation->id,
                            'meal_options' => $item['meal_options'],
                        ]);
                    }
                }
                Session::forget('cart');
                Session::forget('guest_id'); // Очищаємо guest_id після синхронізації
                Log::info('Cleared session cart and guest_id after synchronization', ['user_id' => $userId]);
            }

            $cart = Cart::with(['accommodations.accommodation.city.region', 'accommodations.mealOptions.mealOption'])
                ->where('user_id', $userId)
                ->first();
            Log::info('Reloaded cart after synchronization', [
                'user_id' => $userId,
                'cart_id' => $cart ? $cart->id : null,
                'accommodations_count' => $cart ? $cart->accommodations->count() : 0,
            ]);

            $cartItemsCount = $cart ? $cart->accommodations()->count() : 0;

            if ($cart && $cart->accommodations) {
                foreach ($cart->accommodations as $item) {
                    $checkinDate = \Carbon\Carbon::parse($item->checkin_date);
                    $checkoutDate = \Carbon\Carbon::parse($item->checkout_date);
                    $nights = max(1, abs($checkoutDate->diffInDays($checkinDate)));

                    $accommodationPrice = $item->price * $nights;
                    $mealTotal = $item->mealOptions->sum(function ($cartMealOption) {
                        return ($cartMealOption->price ?? 0) * $cartMealOption->guests_count;
                    });

                    $itemTotal = $accommodationPrice + $mealTotal;
                    $item->itemTotal = $itemTotal;
                    $total += $itemTotal;

                    $regionId = $item->accommodation->city->region_id ?? $item->accommodation->region_id;
                    $services = Service::with('category')
                        ->where('region_id', $regionId)
                        ->get();

                    foreach ($services as $service) {
                        if (
                            $item->accommodation->latitude && $item->accommodation->longitude &&
                            $service->latitude && $service->longitude
                        ) {
                            $distance = $this->calculateDistance(
                                $item->accommodation->latitude,
                                $item->accommodation->longitude,
                                $service->latitude,
                                $service->longitude
                            );
                            $service->distance = round($distance, 2);
                        } else {
                            $service->distance = null;
                            Log::warning('Missing coordinates for distance calculation', [
                                'accommodation_id' => $item->accommodation->id,
                                'service_id' => $service->id,
                                'accommodation_coords' => [
                                    'lat' => $item->accommodation->latitude,
                                    'lon' => $item->accommodation->longitude,
                                ],
                                'service_coords' => [
                                    'lat' => $service->latitude,
                                    'lon' => $service->longitude,
                                ],
                            ]);
                        }
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
                    $nights = max(1, abs($checkoutDate->diffInDays($checkinDate)));

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

                    $regionId = $accommodation->city->region_id ?? $accommodation->region_id;
                    $services = Service::with('category')
                        ->where('region_id', $regionId)
                        ->get();

                    foreach ($services as $service) {
                        if (
                            $accommodation->latitude && $accommodation->longitude &&
                            $service->latitude && $service->longitude
                        ) {
                            $distance = $this->calculateDistance(
                                $accommodation->latitude,
                                $accommodation->longitude,
                                $service->latitude,
                                $service->longitude
                            );
                            $service->distance = round($distance, 2);
                        } else {
                            $service->distance = null;
                            Log::warning('Missing coordinates for distance calculation', [
                                'accommodation_id' => $accommodation->id,
                                'service_id' => $service->id,
                                'accommodation_coords' => [
                                    'lat' => $accommodation->latitude,
                                    'lon' => $accommodation->longitude,
                                ],
                                'service_coords' => [
                                    'lat' => $service->latitude,
                                    'lon' => $service->longitude,
                                ],
                            ]);
                        }
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
        Log::info('Starting checkout method', ['user_id' => Auth::id() ?? 'Guest']);

        $cartItemsCount = 0;
        $total = 0;
        $cartSessionData = [];
        $cartData = json_decode(request()->input('cartData'), true) ?? [];

        if (!Auth::check()) {
            $guestId = Session::get('guest_id');
            $allCartData = Session::get('cart', []);
            $cartSessionData = $guestId ? ($allCartData[$guestId] ?? []) : [];
            Log::info('Checkout cart data for guest', [
                'guest_id' => $guestId,
                'cart_session_data' => $cartSessionData,
                'cart_data' => $cartData,
            ]);
        } else {
            $cartSessionData = Session::get('cart', []);
            Log::info('Checkout cart data for authenticated user', [
                'user_id' => Auth::id(),
                'cart_session_data' => $cartSessionData,
                'cart_data' => $cartData,
            ]);
        }

        Session::put('cartData', $cartData);

        $packages = Package::with('services')->get();

        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::with(['accommodations.accommodation.city.region', 'accommodations.mealOptions.mealOption'])
                ->where('user_id', $userId)
                ->first();
            Log::info('Cart loaded for checkout', [
                'user_id' => $userId,
                'cart_id' => $cart ? $cart->id : null,
                'accommodations_count' => $cart ? $cart->accommodations->count() : 0,
            ]);

            if (!empty($cartSessionData)) {
                if (!$cart) {
                    $cart = Cart::create(['user_id' => $userId]);
                    Log::info('Created new cart for checkout', ['user_id' => $userId, 'cart_id' => $cart->id]);
                }

                foreach ($cartSessionData as $item) {
                    $exists = $cart->accommodations()
                        ->where('accommodation_id', $item['accommodation_id'])
                        ->where('checkin_date', $item['checkin_date'])
                        ->where('checkout_date', $item['checkout_date'])
                        ->where('guests_count', json_encode($item['guests_count']))
                        ->first();

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

                        Log::info('Added session item to DB cart in checkout', [
                            'cart_accommodation_id' => $cartAccommodation->id,
                            'accommodation_id' => $item['accommodation_id'],
                            'checkin_date' => $item['checkin_date'],
                            'checkout_date' => $item['checkout_date'],
                            'guests_count' => $item['guests_count'],
                        ]);

                        if (!empty($item['meal_options'])) {
                            foreach ($item['meal_options'] as $mealOption) {
                                CartAccommodationMealOption::create([
                                    'cart_accommodation_id' => $cartAccommodation->id,
                                    'meal_option_id' => $mealOption['meal_option_id'],
                                    'guests_count' => $mealOption['guests_count'],
                                    'price' => $mealOption['price'] ?? 0,
                                ]);
                            }
                            Log::info('Added meal options from session to DB in checkout', [
                                'cart_accommodation_id' => $cartAccommodation->id,
                                'meal_options' => $item['meal_options'],
                            ]);
                        }
                    } else {
                        Log::warning('Session item already exists in DB cart in checkout', [
                            'accommodation_id' => $item['accommodation_id'],
                            'checkin_date' => $item['checkin_date'],
                            'checkout_date' => $item['checkout_date'],
                            'guests_count' => $item['guests_count'],
                            'existing_cart_accommodation_id' => $exists->id,
                        ]);
                    }
                }
                Session::forget('cart');
                Session::forget('guest_id');
                Log::info('Cleared session cart after checkout synchronization', ['user_id' => $userId]);
            }

            $cart = Cart::with(['accommodations.accommodation.city.region', 'accommodations.mealOptions.mealOption'])
                ->where('user_id', $userId)
                ->first();
            Log::info('Reloaded cart after checkout synchronization', [
                'user_id' => $userId,
                'cart_id' => $cart ? $cart->id : null,
                'accommodations_count' => $cart ? $cart->accommodations->count() : 0,
            ]);

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

                    $regionId = $item->accommodation->city->region_id ?? $item->accommodation->region_id;
                    $services = Service::with('category')
                        ->where('region_id', $regionId)
                        ->get();

                    foreach ($services as $service) {
                        if (
                            $item->accommodation->latitude && $item->accommodation->longitude &&
                            $service->latitude && $service->longitude
                        ) {
                            $distance = $this->calculateDistance(
                                $item->accommodation->latitude,
                                $item->accommodation->longitude,
                                $service->latitude,
                                $service->longitude
                            );
                            $service->distance = round($distance, 2);
                        } else {
                            $service->distance = null;
                            Log::warning('Missing coordinates for distance calculation', [
                                'accommodation_id' => $item->accommodation->id,
                                'service_id' => $service->id,
                                'accommodation_coords' => [
                                    'lat' => $item->accommodation->latitude,
                                    'lon' => $item->accommodation->longitude,
                                ],
                                'service_coords' => [
                                    'lat' => $service->latitude,
                                    'lon' => $service->longitude,
                                ],
                            ]);
                        }
                    }

                    $item->availableServices = $services->groupBy('category_id');
                    $item->serviceCategories = ServiceCategory::all();
                    $item->selectedServices = $cartData[$itemId]['services'] ?? [];
                    $item->selectedPackages = $cartData[$itemId]['packages'] ?? [];
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

                    $regionId = $accommodation->city->region_id ?? $accommodation->region_id;
                    $services = Service::with('category')
                        ->where('region_id', $regionId)
                        ->get();

                    foreach ($services as $service) {
                        if (
                            $accommodation->latitude && $accommodation->longitude &&
                            $service->latitude && $service->longitude
                        ) {
                            $distance = $this->calculateDistance(
                                $accommodation->latitude,
                                $accommodation->longitude,
                                $service->latitude,
                                $service->longitude
                            );
                            $service->distance = round($distance, 2);
                        } else {
                            $service->distance = null;
                            Log::warning('Missing coordinates for distance calculation', [
                                'accommodation_id' => $accommodation->id,
                                'service_id' => $service->id,
                                'accommodation_coords' => [
                                    'lat' => $accommodation->latitude,
                                    'lon' => $accommodation->longitude,
                                ],
                                'service_coords' => [
                                    'lat' => $service->latitude,
                                    'lon' => $service->longitude,
                                ],
                            ]);
                        }
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
                        'selectedServices' => $cartData[$key]['services'] ?? [],
                        'selectedPackages' => $cartData[$key]['packages'] ?? [],
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
            'payment_method' => 'required',
        ]);
    
        $cartData = json_decode($request->input('cartData'), true) ?? [];
        $cartSessionData = [];
        $guestId = Session::get('guest_id');
        $allCartData = Session::get('cart', []);
        if ($guestId) {
            $cartSessionData = $allCartData[$guestId] ?? [];
        }
    
        Log::info('Cart Data on Checkout:', $cartData);
    
        $grandTotal = 0;
        $bookingIds = [];
    
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    
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
    
                Log::info("Booking for Item {$itemId}: Nights = {$nights}, Accommodation Price = {$accommodationPrice}, Meal Total = {$mealTotal}, Service Total = {$serviceTotal}, Package Total = {$packageTotal}, Item Total = {$itemTotal}");
            }
    
            try {
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $grandTotal * 100,
                    'currency' => 'uah',
                    'payment_method' => $request->payment_method,
                    'confirm' => true,
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never',
                    ],
                    'metadata' => [
                        'user_id' => $userId,
                        'order_total' => $grandTotal,
                    ],
                ]);
    
                if ($paymentIntent->status !== 'succeeded') {
                    throw new \Exception('Помилка оплати: платіж не підтверджено.');
                }
    
                foreach ($cart->accommodations as $item) {
                    $itemId = $item->id;
                    $mealTotal = max(0, $item->mealOptions->sum(function ($cartMealOption) {
                        return max(0, ($cartMealOption->price ?? 0)) * max(1, $cartMealOption->guests_count);
                    }));
                    $accommodationPrice = max(0, $item->price) * $nights;
                    $itemTotal = $accommodationPrice + $mealTotal + ($cartData[$itemId]['service_total'] ?? 0) + ($cartData[$itemId]['package_total'] ?? 0);
    
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
                        'payment_intent_id' => $paymentIntent->id,
                    ]);
    
                    $bookingIds[] = $booking->id;
    
                    // Зберігаємо meal_options для цього бронювання
                    if ($item->mealOptions->isNotEmpty()) {
                        foreach ($item->mealOptions as $mealOption) {
                            $booking->mealOptions()->attach($mealOption->meal_option_id, [
                                'price' => $mealOption->price ?? 0,
                                'guests_count' => $mealOption->guests_count,
                            ]);
                        }
                        Log::info('Meal options saved for booking', [
                            'booking_id' => $booking->id,
                            'meal_options' => $item->mealOptions->toArray(),
                        ]);
                    }
    
                    // Додаємо окремо обрані послуги
                    if (isset($cartData[$itemId]['services']) && is_array($cartData[$itemId]['services'])) {
                        foreach ($cartData[$itemId]['services'] as $service) {
                            if (isset($service['id']) && isset($service['price'])) {
                                BookingService::create([
                                    'booking_id' => $booking->id,
                                    'service_id' => $service['id'],
                                    'price' => $service['price'],
                                ]);
                            }
                        }
                    }
    
                    // Додаємо пакети до таблиці booking_packages
                    if (isset($cartData[$itemId]['packages']) && is_array($cartData[$itemId]['packages'])) {
                        foreach ($cartData[$itemId]['packages'] as $packageData) {
                            if (isset($packageData['id']) && isset($packageData['price'])) {
                                BookingPackage::create([
                                    'booking_id' => $booking->id,
                                    'package_id' => $packageData['id'],
                                    'price' => $packageData['price'],
                                ]);
                            }
                        }
                    }
                }
    
                $cart->accommodations()->delete();
                $cart->delete();
            } catch (\Exception $e) {
                Log::error('Payment failed', ['error' => $e->getMessage()]);
                return redirect()->route('cart.checkout')->with('error', 'Помилка оплати: ' . $e->getMessage());
            }
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
    
                    Log::info("Booking for Item {$key} (Guest): Nights = {$nights}, Accommodation Price = {$accommodationPrice}, Meal Total = {$mealTotal}, Service Total = {$serviceTotal}, Package Total = {$packageTotal}, Item Total = {$itemTotal}");
                }
            }
    
            try {
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $grandTotal * 100,
                    'currency' => 'uah',
                    'payment_method' => $request->payment_method,
                    'confirm' => true,
                    'automatic_payment_methods' => [
                        'enabled' => true,
                        'allow_redirects' => 'never',
                    ],
                    'metadata' => [
                        'order_total' => $grandTotal,
                    ],
                ]);
    
                if ($paymentIntent->status !== 'succeeded') {
                    throw new \Exception('Помилка оплати: платіж не підтверджено.');
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
                            'payment_intent_id' => $paymentIntent->id,
                            'token' => Str::random(32),
                        ]);
    
                        $bookingIds[] = $booking->id;
    
                        // Зберігаємо meal_options для цього бронювання (для гостей)
                        if (!empty($item['meal_options'])) {
                            foreach ($item['meal_options'] as $mealOption) {
                                $booking->mealOptions()->attach($mealOption['meal_option_id'], [
                                    'price' => $mealOption['price'] ?? 0,
                                    'guests_count' => $mealOption['guests_count'],
                                ]);
                            }
                            Log::info('Meal options saved for guest booking', [
                                'booking_id' => $booking->id,
                                'meal_options' => $item['meal_options'],
                            ]);
                        }
    
                        // Додаємо окремо обрані послуги
                        if (isset($cartData[$key]['services']) && is_array($cartData[$key]['services'])) {
                            foreach ($cartData[$key]['services'] as $service) {
                                if (isset($service['id']) && isset($service['price'])) {
                                    BookingService::create([
                                        'booking_id' => $booking->id,
                                        'service_id' => $service['id'],
                                        'price' => $service['price'],
                                    ]);
                                }
                            }
                        }
    
                        // Додаємо пакети до таблиці booking_packages
                        if (isset($cartData[$key]['packages']) && is_array($cartData[$key]['packages'])) {
                            foreach ($cartData[$key]['packages'] as $packageData) {
                                if (isset($packageData['id']) && isset($packageData['price'])) {
                                    BookingPackage::create([
                                        'booking_id' => $booking->id,
                                        'package_id' => $packageData['id'],
                                        'price' => $packageData['price'],
                                    ]);
                                }
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('Payment failed', ['error' => $e->getMessage()]);
                return redirect()->route('cart.checkout')->with('error', 'Помилка оплати: ' . $e->getMessage());
            }
        }
    
        Session::put('guest_email', $request->email);
        Session::put('booking_ids', $bookingIds);
    
        Session::forget('cart');
        Session::forget('cartData');
        Session::forget('guest_id');
        Log::info('Fully cleared session after checkout', ['guest_id' => $guestId ?? 'none']);
    
        return redirect()->route('guest.success')->with('success', 'Замовлення успішно оформлено та оплачено!');
    }

    public function remove(Request $request, $id)
    {
        if (Auth::check() && Auth::user()->is_blocked) {
            return $this->jsonResponse(false, 'Ваш обліковий запис заблоковано. Ви не можете видаляти помешкання з кошика.');
        }

        if (Auth::check()) {
            $userId = Auth::id();
            $cart = Cart::where('user_id', $userId)->first();

            if ($cart) {
                $cartAccommodation = $cart->accommodations()->where('id', $id)->first();
                if ($cartAccommodation) {
                    $cartAccommodation->mealOptions()->delete();
                    $cartAccommodation->delete();
                    return $this->jsonResponse(true, 'Елемент успішно видалено з кошика');
                }
                logger()->warning("CartAccommodation with ID {$id} not found for user {$userId}");
                return $this->jsonResponse(false, 'Елемент не знайдено в кошику');
            }
            logger()->warning("Cart not found for user {$userId}");
            return $this->jsonResponse(false, 'Кошик не знайдено');
        } else {
            $guestId = Session::get('guest_id');
            $allCartData = Session::get('cart', []);
            $cartData = $guestId ? ($allCartData[$guestId] ?? []) : [];

            $itemKey = $id; // Для незареєстрованих користувачів $id є ключем у сесії
            if (array_key_exists($itemKey, $cartData)) {
                unset($cartData[$itemKey]);
                $allCartData[$guestId] = $cartData;
                Session::put('cart', $allCartData);
                return $this->jsonResponse(true, 'Елемент успішно видалено з кошика');
            }

            logger()->warning("Cart item with key {$id} not found in session for guest", ['guest_id' => $guestId]);
            return $this->jsonResponse(false, 'Елемент не знайдено в кошику');
        }
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


  

    /**
     * Повертає JSON-відповідь для AJAX-запитів
     */
    private function jsonResponse($success, $message)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
        ]);
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
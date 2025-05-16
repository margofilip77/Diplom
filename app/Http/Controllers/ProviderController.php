<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Accommodation;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiceCategory;
use App\Models\AccommodationPhoto;
use Illuminate\Support\Facades\DB;
use App\Models\AmenityCategory;
use App\Models\MealOption;
use App\Http\Requests\ProviderRegisterRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\BookingService;



class ProviderController extends Controller
{
    private function checkProviderRole()
    {
        if (!Auth::check()) {
            return redirect()->route('provider.auth')->with('error', 'Увійдіть або зареєструйтеся як надавач, щоб отримати доступ до цієї сторінки.');
        }
    
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Ваш обліковий запис заблоковано. Ви не можете отримати доступ до панелі надавача.');
        }
    
        // Перевіряємо, чи запит походить із provider.auth або provider.register
        $currentRoute = request()->route()->getName();
        if (Auth::user()->role !== 'provider' && !in_array($currentRoute, ['provider.auth', 'provider.register'])) {
            return redirect()->route('provider.register')->with('info', 'Ви не зареєстровані як надавач. Пройдіть реєстрацію.');
        }
    
        return null;
    }
    public function dashboard()
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }
    
        $services = Service::where('user_id', Auth::id())->get();
        $accommodations = Accommodation::with(['photos', 'region'])->where('user_id', Auth::id())->get();
    
        $providerId = Auth::id();
        $accommodationIds = Accommodation::where('user_id', $providerId)->pluck('id');
        $activeBookingsCount = Booking::whereIn('accommodation_id', $accommodationIds)
            ->where('checkout_date', '>=', now())
            ->count();
    
        $serviceIds = Service::where('user_id', $providerId)->pluck('id');
        $activeServicesCount = BookingService::whereIn('service_id', $serviceIds)
            ->whereHas('booking', function ($query) {
                $query->where('checkout_date', '>=', now());
            })
            ->count();
    
        $activeOrdersCount = $activeBookingsCount + $activeServicesCount;
    
        return view('provider.dashboard', compact('services', 'accommodations', 'activeOrdersCount'));
    }

    public function orders()
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }
    
        $providerId = Auth::id();
    
        // Отримуємо всі помешкання, які належать надавачу
        $accommodationIds = Accommodation::where('user_id', $providerId)->pluck('id');
    
        // Отримуємо всі бронювання для цих помешкань
        $bookings = Booking::whereIn('accommodation_id', $accommodationIds)
            ->with([
                'accommodation',
                'services.service',
                'packages.package',
                'user',
                'mealOptions' // Додаємо зв’язок із типами харчування
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Отримуємо всі послуги, які належать надавачу
        $serviceIds = Service::where('user_id', $providerId)->pluck('id');
    
        // Отримуємо всі записи з booking_services для цих послуг
        $bookedServices = BookingService::whereIn('service_id', $serviceIds)
            ->with(['booking.accommodation', 'booking.mealOptions', 'service', 'booking.user']) // Додаємо mealOptions для бронювань
            ->orderBy('created_at', 'desc')
            ->get();
    
        // Обчислюємо кількість активних замовлень
        $activeBookingsCount = Booking::whereIn('accommodation_id', $accommodationIds)
            ->where('checkout_date', '>=', now())
            ->count();
    
        $activeServicesCount = BookingService::whereIn('service_id', $serviceIds)
            ->whereHas('booking', function ($query) {
                $query->where('checkout_date', '>=', now());
            })
            ->count();
    
        $activeOrdersCount = $activeBookingsCount + $activeServicesCount;
    
        return view('provider.orders', compact('bookings', 'bookedServices', 'activeOrdersCount'));
    }
    public function showProviderAuth()
    {
        if (Auth::check()) {
            if (Auth::user()->role === 'provider') {
                return redirect()->route('provider.dashboard');
            }
            // Якщо користувач авторизований, але не надавач, показуємо сторінку з повідомленням
            return view('provider.auth')->with('info', 'Ви не зареєстровані як надавач. Ви можете зареєструватися нижче.');
        }
        return view('provider.auth');
    }

    public function register(ProviderRegisterRequest $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'provider') {
                return redirect()->route('provider.dashboard')->with('info', 'Ви вже зареєстровані як надавач.');
            }
            // Завантажуємо користувача з бази даних через Eloquent
            $eloquentUser = \App\Models\User::find($user->id);
            // Оновлюємо роль та інші поля
            $eloquentUser->update([
                'role' => 'provider',
                'name' => $request->input('name', $user->name),
                'phone' => $request->input('phone', $user->phone),
            ]);
            return redirect()->route('provider.dashboard')->with('success', 'Ваша роль оновлена на надавача!');
        }
    
        try {
            $validated = $request->validated();
    
            $user = \App\Models\User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'phone' => $validated['phone'] ?? null,
                'role' => 'provider',
            ]);
    
            Auth::login($user);
    
            return redirect()->route('provider.dashboard')->with('success', 'Ви успішно зареєструвалися як надавач!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Сталася помилка під час реєстрації. Спробуйте ще раз або зверніться до підтримки.')->withInput();
        }
    }

    public function login(Request $request)
    {
        if (!$request->session()->isStarted()) {
            Log::error('Session is not started for login request');
        }
    
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
    
            $user = Auth::user();
            if ($user->role === 'provider') {
                return redirect()->route('provider.dashboard');
            }
    
            // Якщо роль не 'provider', пропонуємо зареєструватися
            return redirect()->route('provider.auth')->with('info', 'Ви не зареєстровані як надавач. Ви можете зареєструватися нижче або звернутися до адміністратора для зміни ролі.');
        }
    
        return back()->withErrors([
            'email' => 'Неправильний email або пароль.',
        ])->onlyInput('email');
    }
   
    public function createService()
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }

        $regions = Region::all();
        $categories = ServiceCategory::all();

        return view('provider.services.create', compact('regions', 'categories'));
    }

    public function storeService(Request $request)
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'region_id' => 'required|string',
            'settlement' => 'required|string|max:255',
            'category_id' => 'required|exists:service_categories,id',
            'duration' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if (is_numeric($validated['region_id'])) {
            $region = Region::findOrFail($validated['region_id']);
        } else {
            $region = Region::firstOrCreate(['name' => $validated['region_id']]);
        }

        $service = new Service($validated);
        $service->user_id = Auth::id();
        $service->region_id = $region->id;
        $service->category_id = $validated['category_id'];
        $service->is_available = true;
        $service->status = 'pending';
        $service->save();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $service->image = $path;
            $service->save();
        }

        return redirect()->route('provider.dashboard')->with('status', 'service-submitted');
    }

    public function createAccommodation()
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }
    
        $regions = Region::all();
        $amenity_categories = AmenityCategory::with('amenities')->get();
        $meal_options = MealOption::all();
    
        return view('provider.accommodations.create', compact('regions', 'amenity_categories', 'meal_options'));
    }
    
    public function storeAccommodation(Request $request)
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }
    
        // Логування для дебагу
        Log::info('Request data:', $request->all());
    
        // Перевірка, чи створюється новий регіон
        $is_new_region = $request->input('region_id') === 'new';
    
        // Обробка регіону
        if ($is_new_region) {
            $request->validate([
                'new_region' => 'required|string|max:255',
            ]);
            $region_name = $request->input('new_region');
            $region = Region::firstOrCreate(['name' => $region_name]);
            $region_id = $region->id;
        } else {
            $request->validate([
                'region_id' => 'required|exists:regions,id',
            ]);
            $region_id = $request->input('region_id');
            $region = Region::find($region_id);
            $region_name = $region->name;
        }
    
        // Базова валідація
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'detailed_description' => 'nullable|string',
            'children' => 'required|in:allowed,not_allowed,has_cribs',
            'beds' => 'required|string|max:255',
            'age_restrictions' => 'required|integer|min:0',
            'pets_allowed' => 'required|in:yes,no',
            'parties_allowed' => 'required|in:yes,no',
            'checkin_time' => 'required',
            'checkout_time' => 'required',
            'settlement' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'required|in:0,1',
            'meal_options' => 'nullable|array',
            'meal_options.*.selected' => 'nullable|in:1',
            'meal_options.*.price' => 'nullable|numeric|min:0',
        ]);
    
        // Створюємо помешкання
        $accommodation = Accommodation::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price_per_night' => $validated['price_per_night'],
            'capacity' => $validated['capacity'],
            'detailed_description' => $validated['detailed_description'],
            'children' => $validated['children'],
            'beds' => $validated['beds'],
            'age_restrictions' => $validated['age_restrictions'],
            'pets_allowed' => $validated['pets_allowed'],
            'parties_allowed' => $validated['parties_allowed'],
            'checkin_time' => $validated['checkin_time'],
            'checkout_time' => $validated['checkout_time'],
            'region_id' => $region_id,
            'region' => $region_name,
            'settlement' => $validated['settlement'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'is_available' => $validated['is_available'],
            'provider_id' => Auth::id(),
            'user_id' => Auth::id(),
            'status' => 'pending',
            'cancellation_policy' => "1. Скасування до 14 днів до заселення – передоплата повертається за вирахуванням банківських комісій.\n2. Скасування менш ніж за 14 днів – передоплата не повертається, оскільки вона покриває витрати виконавця.\n3. Відсутність оплати у встановлений термін – бронювання автоматично анулюється.\n4. Скорочення строку проживання після заселення – виконавець має право отримати компенсацію (1-2 доби оренди) та переглянути загальну вартість послуг відповідно до фактичного строку проживання.\n5. Завершення оренди – відбувається після перевірки стану помешкання виконавцем.",
        ]);
    
        if ($request->has('amenities')) {
            $accommodation->amenities()->attach($request->amenities);
        }
    
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('accommodation_photos', 'public');
                AccommodationPhoto::create([
                    'accommodation_id' => $accommodation->id,
                    'photo_path' => $path,
                ]);
            }
        }
    
        if ($request->has('meal_options')) {
            foreach ($request->meal_options as $mealOptionId => $data) {
                if (isset($data['selected']) && $data['selected'] == '1') {
                    $accommodation->mealOptions()->attach($mealOptionId, [
                        'price' => $data['price'] ?? null,
                    ]);
                }
            }
        }
    
        return redirect()->route('provider.dashboard')->with('status', 'Помешкання успішно створено та очікує на підтвердження.');
    }
    public function editService(Service $service)
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }

        if ($service->user_id !== Auth::id()) {
            return redirect()->route('provider.dashboard')->with('error', 'У вас немає прав для редагування цієї послуги.');
        }

        $regions = Region::all();
        $categories = ServiceCategory::all();

        return view('provider.services.edit', compact('service', 'regions', 'categories'));
    }

    public function updateService(Request $request, Service $service)
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }
    
        if ($service->user_id !== Auth::id()) {
            return redirect()->route('provider.dashboard')->with('error', 'У вас немає прав для редагування цієї послуги.');
        }
    
        // Перевірка, чи створюється новий регіон
        $is_new_region = $request->input('region_id') === 'new';
    
        // Обробка регіону
        if ($is_new_region) {
            $request->validate([
                'new_region' => 'required|string|max:255',
            ]);
            $region_name = $request->input('new_region');
            $region = Region::firstOrCreate(['name' => $region_name]);
            $region_id = $region->id;
        } else {
            $request->validate([
                'region_id' => 'required|exists:regions,id',
            ]);
            $region_id = $request->input('region_id');
        }
    
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'settlement' => 'required|string|max:255',
            'category_id' => 'required|exists:service_categories,id',
            'duration' => 'nullable|integer|min:1',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'image' => 'nullable|image|max:2048',
            'is_available' => 'required|boolean',
        ]);
    
        $serviceData = $validated;
        $serviceData['region_id'] = $region_id;
    
        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $serviceData['image'] = $request->file('image')->store('images', 'public');
        }
    
        if ($service->status === 'rejected') {
            $serviceData['status'] = 'pending';
            $serviceData['rejection_reason'] = null;
        }
    
        $service->update($serviceData);
    
        return redirect()->route('provider.dashboard')->with('status', 'Послугу успішно оновлено.');
    }

    public function editAccommodation(Accommodation $accommodation)
    {
        if (!Auth::check() || Auth::user()->role !== 'provider' || $accommodation->user_id !== Auth::id()) {
            return redirect()->route('provider.dashboard')->with('error', 'У вас немає прав для редагування цього помешкання.');
        }

        $regions = Region::all();
        return view('provider.accommodations.edit', compact('accommodation', 'regions'));
    }

    public function updateAccommodation(Request $request, Accommodation $accommodation)
    {
        if (!Auth::check() || Auth::user()->role !== 'provider' || $accommodation->user_id !== Auth::id()) {
            return redirect()->route('provider.dashboard')->with('error', 'У вас немає прав для редагування цього помешкання.');
        }
    
        // Логування для дебагу
        Log::info('Request data:', $request->all());
    
        // Перевірка, чи створюється новий регіон
        $is_new_region = $request->input('region_id') === 'new';
    
        // Обробка регіону
        if ($is_new_region) {
            $request->validate([
                'new_region' => 'required|string|max:255',
            ]);
            $region_name = $request->input('new_region');
            $region = Region::firstOrCreate(['name' => $region_name]);
            $region_id = $region->id;
        } else {
            $request->validate([
                'region_id' => 'required|exists:regions,id',
            ]);
            $region_id = $request->input('region_id');
            $region = Region::find($region_id);
            $region_name = $region->name;
        }
    
        // Базова валідація
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price_per_night' => 'required|numeric|min:0',
            'capacity' => 'required|integer|min:1',
            'detailed_description' => 'required|string',
            'children' => 'required|in:allowed,not_allowed,has_cribs',
            'beds' => 'required|integer|min:1',
            'age_restrictions' => 'required|integer|min:0',
            'pets_allowed' => 'required|in:yes,no',
            'parties_allowed' => 'required|in:yes,no',
            'checkin_time' => 'required|date_format:H:i',
            'checkout_time' => 'required|date_format:H:i',
            'settlement' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'required|in:0,1',
        ]);
    
        // Оновлюємо дані помешкання
        $validated['is_available'] = (bool) $validated['is_available'];
        $validated['region_id'] = $region_id;
        $validated['region'] = $region_name;
    
        $accommodation->update($validated);
    
        if ($request->hasFile('photos')) {
            foreach ($accommodation->photos as $photo) {
                Storage::disk('public')->delete($photo->photo_path);
                $photo->delete();
            }
    
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('accommodation_photos', 'public');
                AccommodationPhoto::create([
                    'accommodation_id' => $accommodation->id,
                    'photo_path' => $path,
                ]);
            }
        }
    
        $accommodation->update(['status' => 'pending']);
    
        return redirect()->route('provider.dashboard')->with('status', 'Помешкання успішно оновлено та очікує на підтвердження.');
    }

    public function resubmitService(Service $service)
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }

        if ($service->user_id !== Auth::id()) {
            return redirect()->route('provider.dashboard')
                ->with('error', 'У вас немає прав для цієї дії.');
        }

        // Normalize the status by trimming and converting to lowercase
        $currentStatus = trim(strtolower($service->status));
        if ($currentStatus !== 'rejected') {
            return redirect()->route('provider.dashboard')
                ->with('error', 'Ця послуга не потребує повторного надсилання на перевірку.');
        }

        // Use raw SQL to update the status
        $affectedRows = DB::update(
            'UPDATE services SET status = ?, rejection_reason = ?, updated_at = ? WHERE id = ?',
            ['pending', null, now(), $service->id]
        );

        if ($affectedRows === 0) {
            return redirect()->route('provider.dashboard')
                ->with('error', 'Не вдалося оновити статус послуги в базі даних.');
        }

        return redirect()->route('provider.dashboard')
            ->with('status', 'Послугу надіслано на перевірку.');
    }

    public function resubmitAccommodation(Accommodation $accommodation)
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }

        if ($accommodation->user_id !== Auth::id()) {
            return redirect()->route('provider.dashboard')
                ->with('error', 'У вас немає прав для цієї дії.');
        }

        // Normalize the status by trimming and converting to lowercase
        $currentStatus = trim(strtolower($accommodation->status));
        if ($currentStatus !== 'rejected') {
            return redirect()->route('provider.dashboard')
                ->with('error', 'Це помешкання не потребує повторного надсилання на перевірку.');
        }

        // Use raw SQL to update the status
        $affectedRows = DB::update(
            'UPDATE accommodations SET status = ?, rejection_reason = ?, updated_at = ? WHERE id = ?',
            ['pending', null, now(), $accommodation->id]
        );

        if ($affectedRows === 0) {
            return redirect()->route('provider.dashboard')
                ->with('error', 'Не вдалося оновити статус помешкання в базі даних.');
        }

        return redirect()->route('provider.dashboard')
            ->with('status', 'Помешкання надіслано на перевірку.');
    }

    public function destroyService(Service $service)
    {
        $roleCheck = $this->checkProviderRole();
        if ($roleCheck) {
            return $roleCheck;
        }

        if ($service->user_id !== Auth::id()) {
            return redirect()->route('provider.dashboard')->with('error', 'У вас немає прав для видалення цієї послуги.');
        }

        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        $service->delete();

        return redirect()->route('provider.dashboard')->with('status', 'service-deleted');
    }

    public function destroyAccommodation(Accommodation $accommodation)
{
    $roleCheck = $this->checkProviderRole();
    if ($roleCheck) {
        return $roleCheck;
    }

    if ($accommodation->user_id !== Auth::id()) {
        return redirect()->route('provider.dashboard')->with('error', 'У вас немає прав для видалення цього помешкання.');
    }

    // Видаляємо пов’язані фотографії
    foreach ($accommodation->photos as $photo) {
        Storage::disk('public')->delete($photo->photo_path);
        $photo->delete();
    }

    // Видаляємо пов’язані зручності (amenities)
    $accommodation->amenities()->detach();

    // Видаляємо пов’язані типи харчування (meal options)
    $accommodation->mealOptions()->detach();

    // Видаляємо саме помешкання
    $accommodation->delete();

    return redirect()->route('provider.dashboard')->with('status', 'accommodation-deleted');
}
}
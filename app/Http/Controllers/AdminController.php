<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Service;
use App\Models\Accommodation;
use App\Models\Review;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceCategory;
use App\Models\Region;

class AdminController extends Controller
{
    private function checkAdminRole()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'У вас немає доступу до цієї сторінки. Ви повинні бути адміністратором.');
        }
    }

    public function dashboard()
    {
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Ваш обліковий запис заблоковано. Ви не можете отримати доступ до адмін-панелі.');
        }

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'У вас немає доступу до цієї сторінки.');
        }

        return view('admin.dashboard');
    }

    public function users()
    {
        // Перевіряємо, чи користувач є адміністратором
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'У вас немає доступу до цієї сторінки.');
        }

        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function toggleBlockUser(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'У вас немає доступу до цієї дії.');
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')->with('error', 'Ви не можете заблокувати самого себе.');
        }

        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? 'заблоковано' : 'розблоковано';
        return redirect()->route('admin.users')->with('status', "Користувача {$user->name} успішно {$status}.");
    }
    public function updateRole(Request $request, User $user)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('role_error', 'У вас немає доступу до цієї дії.');
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')->with('role_error', 'Ви не можете змінити власну роль.');
        }

        $request->validate([
            'role' => 'required|in:client,admin,provider', // Змінюємо tourist на client
        ]);

        $user->role = $request->input('role');
        $user->save();

        // Перекладаємо назву ролі для повідомлення
        $roleNames = [
            'client' => 'Клієнт',
            'admin' => 'Адміністратор',
            'provider' => 'Надавач',
        ];
        $roleDisplayName = $roleNames[$user->role] ?? $user->role;

        return redirect()->route('admin.users')->with('role_status', "Роль користувача {$user->name} успішно змінено на {$roleDisplayName}.");
    }
    public function reviews()
    {
        $this->checkAdminRole();
        $reviews = Review::with(['user', 'accommodation'])->get();
        return view('admin.reviews', compact('reviews'));
    }

    public function approveReview(Review $review)
    {
        $this->checkAdminRole();
        $review->status = 'approved';
        $review->save();

        return redirect()->route('admin.reviews')->with('status', 'Відгук підтверджено!');
    }

    public function rejectReview(Review $review)
    {
        $this->checkAdminRole();
        $review->status = 'rejected';
        $review->save();

        return redirect()->route('admin.reviews')->with('status', 'Відгук відхилено!');
    }

    public function pendingOffers()
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'У вас немає прав для доступу до цієї сторінки.');
        }

        $services = Service::with(['region', 'category', 'user'])
            ->where('status', 'pending')
            ->get();

        $accommodations = Accommodation::with(['user', 'photos'])
            ->where('status', 'pending')
            ->get();

        return view('admin.pending-offers', compact('services', 'accommodations'));
    }

    public function approveService(Service $service)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'У вас немає прав для цієї дії.');
        }

        $service->update(['status' => 'approved']);
        return redirect()->route('admin.pending-offers')->with('status', 'Послугу успішно підтверджено.');
    }

    public function rejectService(Request $request, Service $service)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'У вас немає прав для цієї дії.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $service->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('admin.pending-offers')->with('status', 'Послугу відхилено.');
    }

    public function approveAccommodation(Accommodation $accommodation)
    {
        $this->checkAdminRole();
        $accommodation->status = 'approved';
        $accommodation->save();

        return redirect()->route('admin.pending-offers')->with('status', 'Помешкання підтверджено!');
    }

    public function rejectAccommodation(Request $request, Accommodation $accommodation)
    {
        $roleCheck = $this->checkAdminRole();
        if ($roleCheck) {
            return $roleCheck;
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $accommodation->status = 'rejected';
        $accommodation->rejection_reason = $request->input('rejection_reason');
        $accommodation->save();

        return redirect()->route('admin.pending-offers')->with('status', 'Помешкання відхилено.');
    }
    public function packages()
    {
        $this->checkAdminRole();
        $packages = Package::with('services')->get();
        return view('admin.packages', compact('packages'));
    }
    
    public function createPackage()
    {
        $this->checkAdminRole();
        $categories = ServiceCategory::with(['services' => function ($query) {
            $query->where('status', 'approved')->with('region');
        }])->get();
        $regions = Region::all();

        if ($categories->pluck('services')->flatten()->isEmpty()) {
            return redirect()->route('admin.packages')->with('error', 'Немає затверджених послуг для створення пакета.');
        }

        return view('admin.packages.create', compact('categories', 'regions'));
    }

    public function storePackage(Request $request)
    {
        $this->checkAdminRole();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'region_id' => 'required|exists:regions,id',
            'discount' => 'required|numeric|min:5|max:10',
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services,id',
        ]);

        // Перевіряємо, що всі послуги належать до обраного регіону
        $services = Service::whereIn('id', $validated['services'])->get();
        foreach ($services as $service) {
            if ($service->region_id != $validated['region_id']) {
                return back()->with('error', 'Обрані послуги повинні належати до обраного регіону.');
            }
        }

        $package = Package::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'region_id' => $validated['region_id'],
            'discount' => $validated['discount'],
        ]);

        $package->services()->attach($validated['services']);
        $package->price = $package->calculatePrice();
        $package->save();

        return redirect()->route('admin.packages')->with('status', 'Пакет створено!');
    }
    /**
     * Форма для редагування пакета
     */
    public function editPackage($id)
    {
        $this->checkAdminRole();
        $package = Package::with('services')->findOrFail($id);
        $categories = ServiceCategory::with(['services' => function ($query) {
            $query->where('status', 'approved')->with('region');
        }])->get();
    
        if ($categories->pluck('services')->flatten()->isEmpty()) {
            return redirect()->route('admin.packages')->with('error', 'Немає затверджених послуг для редагування пакета.');
        }
    
        return view('admin.packages.edit', compact('package', 'categories'));
    }
   

   

    /**
     * Оновлення пакета
     */
    public function updatePackage(Request $request, $id)
    {
        $this->checkAdminRole();
        $package = Package::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount' => 'required|numeric|min:0|max:100',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
        ]);

        $package->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'discount' => $validated['discount'],
        ]);

        $package->services()->sync($validated['services']);
        $package->price = $package->calculatePrice();
        $package->save();

        return redirect()->route('admin.packages')->with('status', 'Пакет оновлено!');
    }

    /**
     * Видалення пакета
     */
    public function deletePackage($id)
    {
        $this->checkAdminRole();
        $package = Package::findOrFail($id);
        $package->services()->detach();
        $package->delete();

        return redirect()->route('admin.packages')->with('status', 'Пакет видалено!');
    }
    public function deleteReview(Review $review)
    {
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Ваш обліковий запис заблоковано. Ви не можете виконувати цю дію.');
        }

        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'У вас немає доступу до цієї дії.');
        }

        $review->delete();

        return redirect()->route('admin.reviews')->with('status', 'Відгук успішно видалено.');
    }
}
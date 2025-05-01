```blade
@extends('layouts.app')

@section('content')
<div class="container my-5">
    <!-- Toast-повідомлення -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
        @if (session('success'))
            <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="true" data-bs-delay="3000">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Заголовок -->
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold" style="color: #1A73E8;">Оформлення замовлення</h1>
        <p class="text-muted lead">Заповніть дані для завершення вашої еко-подорожі</p>
    </div>

    <div class="row">
        <!-- Ліва частина: Форма для введення даних -->
        <div class="col-md-7">
            <div class="card shadow-sm p-4" style="border-radius: 15px;">
                <h3 class="mb-4" style="color: #1A1A1A;">Ваші контактні дані</h3>
                <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cartData" id="cartData" value="">
                    <div class="mb-3">
                        <label for="name" class="form-label" style="color: #6B7280;">Ім'я</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label" style="color: #6B7280;">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label" style="color: #6B7280;">Телефон</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+380-XXX-XXX-XX-XX" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="comments" class="form-label" style="color: #6B7280;">Коментарі до замовлення (необов’язково)</label>
                        <textarea class="form-control" id="comments" name="comments" rows="3">{{ old('comments') }}</textarea>
                    </div>
                    <button type="submit" class="btn w-100" style="background: #28a745; color: #FFFFFF; border-radius: 25px; padding: 10px 20px;">Підтвердити замовлення</button>
                </form>
            </div>
        </div>

        <!-- Права частина: Підсумок замовлення -->
        <div class="col-md-5">
            <div class="card shadow-sm p-4" style="border-radius: 15px;">
                <h3 class="mb-4" style="color: #1A1A1A;">Підсумок замовлення</h3>
                @if(!empty($cart->accommodations) && $cart->accommodations->isNotEmpty())
                    @php
                        $grandTotal = 0;
                    @endphp
                    @foreach($cart->accommodations as $key => $accommodation)
                        @php
                            $guests = is_string($accommodation->guests_count) ? json_decode($accommodation->guests_count, true) : $accommodation->guests_count;
                            $checkinDate = \Carbon\Carbon::parse($accommodation->checkin_date);
                            $checkoutDate = \Carbon\Carbon::parse($accommodation->checkout_date);
                            $nights = max(1, abs($checkinDate->diffInDays($checkoutDate)));
                            $accommodationPrice = max(0, $accommodation->price) * $nights;
                            $mealTotal = max(0, $accommodation->mealOptions->sum(function ($cartMealOption) {
                                return max(0, ($cartMealOption->price ?? 0)) * max(1, $cartMealOption->guests_count);
                            }));
                            $itemId = isset($accommodation->id) ? $accommodation->id : $key;
                            $serviceTotal = max(0, $cartData[$itemId]['service_total'] ?? 0);
                            $packageTotal = max(0, $cartData[$itemId]['package_total'] ?? 0);
                            $services = $cartData[$itemId]['services'] ?? [];
                            $selectedPackages = $cartData[$itemId]['packages'] ?? [];
                            $itemTotal = $accommodationPrice + $mealTotal + $serviceTotal + $packageTotal;
                            $grandTotal += $itemTotal;
                        @endphp

                        <div class="mb-3">
                            <h5 style="color: #1A73E8;">{{ isset($accommodation->accommodation) ? $accommodation->accommodation->name : 'Помешкання' }}</h5>
                            <p class="text-muted" style="font-size: 0.9rem;">
                                <i class="fas fa-calendar-alt me-1" style="color: #FF6200;"></i>
                                {{ $checkinDate->format('Y-m-d') }} - {{ $checkoutDate->format('Y-m-d') }} ({{ $nights }} ночей)
                            </p>
                            <p class="text-muted mb-1" style="font-size: 0.85rem;">
                                Ціна помешкання: {{ number_format($accommodationPrice, 2, '.', '') }} грн
                            </p>
                            @if(!empty($accommodation->mealOptions))
                                <div class="mt-2">
                                    <h6 style="font-size: 0.95rem; color: #1A73E8;">Обране харчування:</h6>
                                    @foreach($accommodation->mealOptions as $cartMealOption)
                                        @php
                                            $meal = $cartMealOption->mealOption ?? (object) ['name' => 'Невідомий тип'];
                                            $price = max(0, $cartMealOption->price ?? 0);
                                        @endphp
                                        <p class="text-muted mb-1" style="font-size: 0.85rem;">
                                            {{ $meal->name }} ({{ $cartMealOption->guests_count }} гостей) - {{ number_format($price * $cartMealOption->guests_count, 2, '.', '') }} грн
                                        </p>
                                    @endforeach
                                    <p class="text-muted mb-1" style="font-size: 0.85rem;">
                                        Загальна ціна за харчування: {{ number_format($mealTotal, 2, '.', '') }} грн
                                    </p>
                                </div>
                            @endif
                            @if(!empty($services))
                                <div class="mt-2">
                                    <h6 style="font-size: 0.95rem; color: #1A73E8;">Обрані додаткові послуги:</h6>
                                    @foreach($services as $service)
                                        @php
                                            $serviceModel = \App\Models\Service::find($service['id']);
                                            $serviceImage = $serviceModel ? asset('services/' . $serviceModel->image) : asset('services/default.jpg');
                                        @endphp
                                        <p class="text-muted mb-1 d-flex align-items-center" style="font-size: 0.85rem;">
                                            <img src="{{ $serviceImage }}" alt="{{ $service['name'] }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 5px; margin-right: 8px;">
                                            {{ $service['name'] }} - {{ number_format(max(0, $service['price']), 2, '.', '') }} грн
                                            @if(str_contains(strtolower($service['name']), 'трансфер'))
                                                <i class="fas fa-car ms-2" style="color: #28a745;"></i>
                                            @elseif(str_contains(strtolower($service['name']), 'еко'))
                                                <i class="fas fa-leaf ms-2" style="color: #28a745;"></i>
                                            @endif
                                        </p>
                                    @endforeach
                                    <p class="text-muted mb-1" style="font-size: 0.85rem;">
                                        Загальна ціна за послуги: {{ number_format($serviceTotal, 2, '.', '') }} грн
                                    </p>
                                </div>
                            @else
                                <div class="mt-2">
                                    <h6 style="font-size: 0.95rem; color: #1A73E8;">Обрані додаткові послуги:</h6>
                                    <p class="text-muted mb-1" style="font-size: 0.85rem;">Послуги не обрані.</p>
                                </div>
                            @endif
                            @if(!empty($selectedPackages))
                                <div class="mt-2">
                                    <h6 style="font-size: 0.95rem; color: #1A73E8;">Обрані пакети:</h6>
                                    @foreach($selectedPackages as $packageData)
                                        @php
                                            $package = $packages->firstWhere('id', $packageData['id']);
                                        @endphp
                                        <div class="mb-2">
                                            <p class="text-muted mb-1 d-flex align-items-center" style="font-size: 0.85rem;">
                                                <span class="badge bg-green-100 text-green-800 text-xs me-2">Еко-тур</span>
                                                {{ $packageData['name'] }} - {{ number_format(max(0, $packageData['price']), 2, '.', '') }} грн
                                            </p>
                                            @if($package && $package->services->isNotEmpty())
                                                <ul class="list-unstyled" style="font-size: 0.85rem;">
                                                    @foreach($package->services as $service)
                                                        <li class="mb-1 d-flex align-items-center">
                                                            <img src="{{ asset('services/' . $service->image) }}" alt="{{ $service->name }}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 5px; margin-right: 8px;">
                                                            <span>{{ $service->name }} ({{ number_format(max(0, $service->price), 2, '.', '') }} грн)</span>
                                                            @if(str_contains(strtolower($service['name']), 'трансфер'))
                                                                <i class="fas fa-car ms-2" style="color: #28a745;"></i>
                                                            @elseif(str_contains(strtolower($service['name']), 'еко'))
                                                                <i class="fas fa-leaf ms-2" style="color: #28a745;"></i>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    @endforeach
                                    <p class="text-muted mb-1" style="font-size: 0.85rem;">
                                        Загальна ціна за пакети: {{ number_format($packageTotal, 2, '.', '') }} грн
                                    </p>
                                </div>
                            @else
                                <div class="mt-2">
                                    <h6 style="font-size: 0.95rem; color: #1A73E8;">Обрані пакети:</h6>
                                    <p class="text-muted mb-1" style="font-size: 0.85rem;">Пакети не обрані.</p>
                                </div>
                            @endif
                            <p class="fw-bold" style="font-size: 1rem; color: #1A1A1A;">Сума: {{ number_format(max(0, $itemTotal), 2, '.', '') }} грн</p>
                        </div>
                    @endforeach

                    <hr>
                    <h4 class="fw-bold" style="color: #1A73E8;">Загальна сума: {{ number_format(max(0, $grandTotal), 2, '.', '') }} грн</h4>
                @else
                    <p class="text-muted">Ваш кошик порожній.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Підключення Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Ініціалізація Toast-повідомлень
    const toastElList = document.querySelectorAll('.toast');
    toastElList.forEach(toastEl => {
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
    });

    // Передача cartData у форму
    const checkoutForm = document.getElementById('checkoutForm');
    const cartDataInput = document.getElementById('cartData');
    const cartData = localStorage.getItem('cartData') || '{}';
    cartDataInput.value = cartData;

    // Логування для дебагінгу
    console.log('Cart Data in checkout:', JSON.parse(cartData));

    // Очищення localStorage після відправки форми
    checkoutForm.addEventListener('submit', function () {
        localStorage.removeItem('cartData');
    });

    // Маска для поля телефону
    const phoneInput = document.getElementById('phone');
    phoneInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('380')) {
            value = value.substring(3);
        }
        if (value.length > 0) {
            value = '+380' + value;
        }
        if (value.length > 3 && value.length <= 6) {
            value = value.replace(/(\+380)(\d{1,3})/, '$1-$2');
        } else if (value.length > 6 && value.length <= 9) {
            value = value.replace(/(\+380)(\d{3})(\d{1,3})/, '$1-$2-$3');
        } else if (value.length > 9) {
            value = value.replace(/(\+380)(\d{3})(\d{3})(\d{1,4})/, '$1-$2-$3-$4');
        }
        e.target.value = value.substring(0, 13); // Обмеження довжини
    });
});
</script>

<style>
.form-control {
    border-radius: 10px;
    border: 1px solid #E5E7EB;
    padding: 10px;
}
.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
}
.btn {
    transition: all 0.3s ease;
}
.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
.badge.bg-green-100 {
    background-color: #D1FAE5;
}
.badge.text-green-800 {
    color: #065F46;
}
.invalid-feedback {
    font-size: 0.85rem;
}
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}
</style>
@endsection
```
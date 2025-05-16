@extends('layouts.app')

@section('content')
<div class="container my-5">
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

    <div class="checkout-container shadow-sm" style="background: #FFFFFF; border-radius: 20px; padding: 30px;">
        <h2 class="text-center fw-bold mb-4" style="color: #1A1A1A;">Оформлення замовлення</h2>

        @if ($cart->accommodations->isEmpty())
        <div class="empty-cart text-center py-5">
            <i class="fas fa-shopping-cart fa-3x mb-3" style="color: #FF6200;"></i>
            <h3 class="fw-bold" style="color: #1A1A1A;">Ваш кошик порожній</h3>
            <p class="text-muted">Додайте помешкання для початку оформлення!</p>
            <a href="{{ route('accommodations.index') }}" class="btn" style="background: #1A73E8; color: #FFFFFF; border-radius: 25px; padding: 10px 20px;">Переглянути помешкання</a>
        </div>
        @else
        <form id="payment-form" action="{{ route('cart.storeCheckout') }}" method="POST">
            @csrf
            <input type="hidden" name="cartData" value="{{ json_encode($cartData) }}">

            <!-- Список помешкань -->
            @foreach ($cart->accommodations as $key => $item)
            @php
                $checkinDate = \Carbon\Carbon::parse($item->checkin_date);
                $checkoutDate = \Carbon\Carbon::parse($item->checkout_date);
                $nights = max(1, $checkinDate->diffInDays($checkoutDate, false));
                $mealTotal = $item->mealOptions->sum(function ($cartMealOption) {
                    return max(0, ($cartMealOption->price ?? 0)) * max(1, $cartMealOption->guests_count);
                });
                $itemId = isset($item->id) ? $item->id : $key;
                $serviceTotal = max(0, $cartData[$itemId]['service_total'] ?? 0);
                $packageTotal = max(0, $cartData[$itemId]['package_total'] ?? 0);
                $itemTotal = $item->itemTotal;
            @endphp

            <div class="cart-item mb-4 p-3" style="border-bottom: 1px solid #E5E7EB;">
                <div class="d-flex align-items-start">
                    <img src="{{ asset($item->accommodation_photo) }}" alt="{{ $item->accommodation->name }}" style="width: 200px; height: 120px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                    <div class="item-details flex-grow-1 ms-4">
                        <h3 class="fw-bold mb-2" style="font-size: 1.2rem; color: #1A1A1A;">{{ $item->accommodation->name }}</h3>
                        <p class="mb-1" style="font-size: 0.9rem; color: #6B7280;">
                            <i class="fas fa-calendar-alt me-1" style="color: #FF6200;"></i>
                            {{ $item->checkin_date }} - {{ $item->checkout_date }} ({{ $nights }} ночей)
                        </p>
                        @if ($item->mealOptions->isNotEmpty())
                        <div class="mt-2">
                            <h4 style="font-size: 1rem; color: #1A73E8;">Обране харчування:</h4>
                            @foreach ($item->mealOptions as $cartMealOption)
                            <p class="mb-1" style="font-size: 0.9rem; color: #6B7280;">
                                {{ $cartMealOption->mealOption->name }} ({{ $cartMealOption->guests_count }} гостей) - {{ $cartMealOption->price * $cartMealOption->guests_count }} грн
                            </p>
                            @endforeach
                        </div>
                        @endif
                        @if (!empty($item->selectedServices))
                        <div class="mt-2">
                            <h4 style="font-size: 1rem; color: #1A73E8;">Обрані послуги:</h4>
                            @foreach ($item->selectedServices as $service)
                            <p class="mb-1" style="font-size: 0.9rem; color: #6B7280;">
                                {{ $service['name'] }} - {{ $service['price'] }} грн
                            </p>
                            @endforeach
                        </div>
                        @endif
                        @if (!empty($item->selectedPackages))
                        <div class="mt-2">
                            <h4 style="font-size: 1rem; color: #1A73E8;">Обрані пакети:</h4>
                            @foreach ($item->selectedPackages as $package)
                            <p class="mb-1" style="font-size: 0.9rem; color: #6B7280;">
                                {{ $package['name'] }} - {{ $package['price'] }} грн
                            </p>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="text-end">
                        <p class="item-price mt-2" style="font-size: 1rem; color: #1A73E8; font-weight: 600;">{{ $itemTotal }} грн</p>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Форма оплати -->
            <div class="payment-details mt-4">
                <h3 class="fw-bold mb-3" style="color: #1A1A1A;">Деталі оплати</h3>
                <div class="mb-3">
                    <label for="name" class="form-label">Ім'я</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Електронна пошта</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Телефон</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>

                <!-- Stripe Elements для введення даних картки -->
                <div class="mb-3">
                    <label for="card-element" class="form-label">Дані картки</label>
                    <div id="card-element" class="form-control" style="height: auto; padding: 10px;"></div>
                    <div id="card-errors" role="alert" class="text-danger mt-2"></div>
                </div>

                <p class="fw-bold mb-2" style="color: #1A73E8;">Загальна сума: {{ $total }} грн</p>
                <button type="submit" id="submit-payment" class="btn" style="background: #28a745; color: #FFFFFF; border-radius: 25px; padding: 10px 20px;">Сплатити та підтвердити</button>
            </div>
        </form>
        @endif
    </div>
</div>

<!-- Підключення Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Передаємо ключ через PHP до JavaScript
    const stripeKey = "{{ config('services.stripe.key') }}";
    const stripe = Stripe(stripeKey);
    const elements = stripe.elements();

    // Створення елементу для введення даних картки з прихованим полем поштового індексу
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#32325d',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#fa755a',
            },
        },
        hidePostalCode: true, // Приховуємо поле поштового індексу
    });
    cardElement.mount('#card-element');

    // Обробка помилок введення даних картки
    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Обробка відправки форми
    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-payment');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        submitButton.disabled = true;

        const { paymentMethod, error } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
            billing_details: {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
            },
        });

        if (error) {
            const displayError = document.getElementById('card-errors');
            displayError.textContent = error.message;
            submitButton.disabled = false;
            return;
        }

        const paymentMethodInput = document.createElement('input');
        paymentMethodInput.setAttribute('type', 'hidden');
        paymentMethodInput.setAttribute('name', 'payment_method');
        paymentMethodInput.setAttribute('value', paymentMethod.id);
        form.appendChild(paymentMethodInput);

        form.submit();
    });
</script>

<style>
    .checkout-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .cart-item:hover {
        background: #F9FAFB;
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .empty-cart .btn:hover {
        background: #1557B0;
    }

    #card-element {
        border: 1px solid #ced4da;
        border-radius: 5px;
        padding: 10px;
    }
</style>
@endsection
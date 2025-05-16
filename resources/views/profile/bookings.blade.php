@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-center mb-0">Мої бронювання</h2>
        <div class="text-end">
            <h4 style="color: #1A1A1A;">Загальна сума: <strong>{{ number_format($totalAmount, 2) }} грн</strong></h4>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if ($bookings->isEmpty())
    <div class="text-center py-5">
        <i class="fas fa-ticket-alt fa-3x mb-3" style="color: #FF6200;"></i>
        <h3 class="fw-bold" style="color: #1A1A1A;">У вас ще немає бронювань</h3>
        <p class="text-muted">Забронюйте помешкання, щоб вони тут з’явилися!</p>
        <a href="{{ route('accommodations.index') }}" class="btn" style="background: #1A73E8; color: #FFFFFF; border-radius: 25px; padding: 10px 20px;">Переглянути помешкання</a>
    </div>
    @else
    <div class="row">
        @foreach ($bookings as $booking)
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm position-relative" style="border-radius: 15px;">
                <!-- Загальна сума в кутку -->
                <div class="position-absolute top-0 end-0 m-3" style="font-size: 1rem; color: #1A73E8; font-weight: 600;" id="totalPriceCard{{ $booking->id }}">
                    {{ number_format($booking->total_price, 2) }} грн
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold" style="color: #1A1A1A;">Бронювання #{{ $booking->id }}</h5>
                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Помешкання:</strong> {{ $booking->accommodation->name ?? 'Невідомо' }}
                    </p>
                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Дата заїзду:</strong> {{ \Carbon\Carbon::parse($booking->checkin_date)->format('d.m.Y') }}
                    </p>
                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Дата виїзду:</strong> {{ \Carbon\Carbon::parse($booking->checkout_date)->format('d.m.Y') }}
                    </p>
                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Кількість ночей:</strong> {{ \Carbon\Carbon::parse($booking->checkin_date)->diffInDays(\Carbon\Carbon::parse($booking->checkout_date)) }}
                    </p>
                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Вартість за ніч:</strong> {{ $booking->accommodation->price_per_night ?? 'Невідомо' }} грн
                    </p>

                    <!-- Обрані послуги -->
                    @if ($booking->services->isNotEmpty())
                    <div class="mt-2">
                        <h6 style="font-size: 1rem; color: #1A73E8;">Обрані послуги:</h6>
                        @foreach ($booking->services as $bookingService)
                        @if ($bookingService->service)
                        <p class="mb-1" style="font-size: 0.9rem; color: #6B7280;">
                            {{ $bookingService->service->name ?? 'Невідома послуга' }} - {{ $bookingService->price ?? '0' }} грн
                        </p>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    <!-- Обрані пакети -->
                    @if ($booking->packages->isNotEmpty())
                    <div class="mt-2">
                        <h6 style="font-size: 1rem; color: #1A73E8;">Обрані пакети:</h6>
                        @foreach ($booking->packages as $bookingPackage)
                        @if ($bookingPackage->package)
                        <p class="mb-1" style="font-size: 0.9rem; color: #6B7280;">
                            {{ $bookingPackage->package->name ?? 'Невідомий пакет' }} - {{ $bookingPackage->price ?? '0' }} грн
                        </p>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    <!-- Обрані типи харчування -->
                    @if ($booking->mealOptions->isNotEmpty())
                    <div class="mt-2">
                        <h6 style="font-size: 1rem; color: #1A73E8;">Обрані типи харчування:</h6>
                        @foreach ($booking->mealOptions as $mealOption)
                        <p class="mb-1" style="font-size: 0.9rem; color: #6B7280;">
                            {{ $mealOption->name ?? 'Невідомий тип' }} ({{ $mealOption->pivot->guests_count ?? 0 }} порцій) - {{ ($mealOption->pivot->price ?? 0) * ($mealOption->pivot->guests_count ?? 0) }} грн
                        </p>
                        @endforeach
                        <p class="mt-1" style="font-size: 0.9rem; color: #1A73E8;">
                            <strong>Загальна кількість порцій:</strong> {{ $booking->mealOptions->sum('pivot.guests_count') ?? 0 }}
                        </p>
                    </div>
                    @endif

                    <!-- Додаткові зручності -->
                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Додаткові зручності:</strong>
                        @if ($booking->accommodation->amenities)
                        @foreach ($booking->accommodation->amenities as $amenity)
                        {{ $amenity->name ?? 'Невідомо' }}{{ !$loop->last ? ', ' : '' }}
                        @endforeach
                        @else
                        Немає додаткових зручностей
                        @endif
                    </p>

                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Ім'я:</strong> {{ $booking->name }}
                    </p>
                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Email:</strong> {{ $booking->email }}
                    </p>
                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Телефон:</strong> {{ $booking->phone }}
                    </p>
                    @if ($booking->comments)
                    <p class="mb-1" style="color: #6B7280;">
                        <strong>Коментарі:</strong> {{ $booking->comments }}
                    </p>
                    @endif

                    @php
                    $checkinDate = \Carbon\Carbon::parse($booking->checkin_date);
                    $currentDate = \Carbon\Carbon::now();
                    $daysUntilCheckin = $currentDate->diffInDays($checkinDate, false);
                    $canCancelOrEdit = $daysUntilCheckin > 14;
                    @endphp

                    <div class="d-flex justify-content-between mt-3">
                        <form action="{{ route('booking.cancel', $booking->id) }}" method="POST" onsubmit="return confirm('Ви впевнені, що хочете скасувати це бронювання?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="border-radius: 25px;" @if(!$canCancelOrEdit) disabled @endif>
                                Скасувати
                            </button>
                        </form>
                        <button class="btn btn-primary" style="border-radius: 25px;" data-bs-toggle="modal" data-bs-target="#editDatesModal{{ $booking->id }}" @if(!$canCancelOrEdit) disabled @endif>
                            Змінити дати
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editDatesModal{{ $booking->id }}" tabindex="-1" aria-labelledby="editDatesModalLabel{{ $booking->id }}" aria-hidden="true" 
            data-price-per-night="{{ $booking->accommodation->price_per_night ?? 0 }}"
            data-services-price="{{ $booking->services->sum('price') ?? 0 }}"
            data-packages-price="{{ $booking->packages->sum('price') ?? 0 }}"
            data-meals-price="{{ $booking->mealOptions->sum(function($mealOption) { return ($mealOption->pivot->price ?? 0) * ($mealOption->pivot->guests_count ?? 0); }) }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDatesModalLabel{{ $booking->id }}">Змінити дати бронювання #{{ $booking->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('booking.update.dates', $booking->id) }}" method="POST" id="editDatesForm{{ $booking->id }}">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="checkin_date_{{ $booking->id }}" class="form-label">Дата заїзду</label>
                                <input type="date" class="form-control checkin-date" id="checkin_date_{{ $booking->id }}" name="checkin_date" value="{{ $booking->checkin_date }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="checkout_date_{{ $booking->id }}" class="form-label">Дата виїзду</label>
                                <input type="date" class="form-control checkout-date" id="checkout_date_{{ $booking->id }}" name="checkout_date" value="{{ $booking->checkout_date }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Нова загальна сума:</label>
                                <p class="new-total-price" id="newTotalPrice{{ $booking->id }}" style="color: #1A73E8; font-weight: 600;">{{ number_format($booking->total_price, 2) }} грн</p>
                            </div>
                            <button type="submit" class="btn btn-primary" style="border-radius: 25px;">Зберегти зміни</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<style>
    .card:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15) !important;
    }

    .btn:hover {
        background: #1557B0;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn:disabled {
        background: #cccccc;
        cursor: not-allowed;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Оновлення загальної суми при зміні дат
        document.querySelectorAll('.modal').forEach(modal => {
            const bookingId = modal.id.replace('editDatesModal', '');
            const checkinInput = modal.querySelector(`#checkin_date_${bookingId}`);
            const checkoutInput = modal.querySelector(`#checkout_date_${bookingId}`);
            const newTotalPrice = modal.querySelector(`#newTotalPrice${bookingId}`);
            const totalPriceCard = document.getElementById(`totalPriceCard${bookingId}`);
            const pricePerNight = parseFloat(modal.getAttribute('data-price-per-night')); // Ціна за ніч
            const servicesPrice = parseFloat(modal.getAttribute('data-services-price')); // Вартість послуг
            const packagesPrice = parseFloat(modal.getAttribute('data-packages-price')); // Вартість пакетів
            const mealsPrice = parseFloat(modal.getAttribute('data-meals-price')); // Вартість харчування

            function updateTotalPrice() {
                const checkin = new Date(checkinInput.value);
                const checkout = new Date(checkoutInput.value);
                if (checkin && checkout && checkin < checkout) {
                    const nights = Math.ceil((checkout - checkin) / (1000 * 60 * 60 * 24));
                    const accommodationCost = nights * pricePerNight;
                    const total = accommodationCost + servicesPrice + packagesPrice + mealsPrice;
                    newTotalPrice.textContent = `${total.toFixed(2)} грн`;
                    totalPriceCard.textContent = `${total.toFixed(2)} грн`; // Оновлення суми на картці
                } else {
                    newTotalPrice.textContent = 'Невірний діапазон дат';
                    totalPriceCard.textContent = 'Невірний діапазон дат'; // Оновлення суми на картці
                }
            }

            checkinInput.addEventListener('change', updateTotalPrice);
            checkoutInput.addEventListener('change', updateTotalPrice);

            // Ініціалізація при завантаженні модального вікна
            modal.addEventListener('shown.bs.modal', updateTotalPrice);

            // Оновлення суми на картці при закритті модального вікна
            modal.addEventListener('hidden.bs.modal', function () {
                const finalTotal = newTotalPrice.textContent.replace(' грн', '').trim();
                if (!isNaN(parseFloat(finalTotal))) {
                    totalPriceCard.textContent = `${parseFloat(finalTotal).toFixed(2)} грн`;
                }
            });
        });

        // Відправка форми з оновленою сумою
        document.querySelectorAll('[id^="editDatesForm"]').forEach(form => {
            form.addEventListener('submit', function (e) {
                const bookingId = this.id.replace('editDatesForm', '');
                const newTotalPriceElement = document.getElementById(`newTotalPrice${bookingId}`);
                let newTotalPriceValue = newTotalPriceElement.textContent.replace(' грн', '').trim();
                
                // Перевірка, чи значення є числовим
                if (isNaN(parseFloat(newTotalPriceValue))) {
                    e.preventDefault();
                    alert('Помилка: Загальна сума не є числовим значенням. Перевірте дати.');
                    return;
                }

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'new_total_price';
                hiddenInput.value = parseFloat(newTotalPriceValue).toFixed(2);
                this.appendChild(hiddenInput);
            });
        });
    });
</script>
@endsection
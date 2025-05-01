@extends('layouts.app')

@section('content')
<div class="container mt-4 d-flex flex-column align-items-center">

    {{-- Галерея фото --}}
    <div class="photo-gallery d-flex">
        @if($accommodation->photos->isNotEmpty())
        @php
        $mainPhoto = $accommodation->photos->first();
        // Визначаємо, чи шлях згенерований Laravel Storage
        $isStoragePath = Str::startsWith($mainPhoto->photo_path, ['accommodation_photos/', 'photos/']);
        $mainPhotoUrl = $isStoragePath ? asset('storage/' . $mainPhoto->photo_path) : asset($mainPhoto->photo_path);
        @endphp
        <div class="main-photo-wrapper">
            <a href="{{ $mainPhotoUrl }}" data-lightbox="gallery" data-title="Головне фото помешкання">
                <img src="{{ $mainPhotoUrl }}"
                    class="main-photo shadow-lg rounded"
                    alt="Головне фото помешкання">
            </a>
        </div>
        @endif

        <div class="side-photos d-flex flex-wrap ms-3">
            @foreach($accommodation->photos->skip(1)->take(4) as $key => $photo)
            @php
            // Визначаємо, чи шлях згенерований Laravel Storage
            $isStoragePath = Str::startsWith($photo->photo_path, ['accommodation_photos/', 'photos/']);
            $photoUrl = $isStoragePath ? asset('storage/' . $photo->photo_path) : asset($photo->photo_path);
            @endphp
            <div class="photo-wrapper position-relative">
                <a href="{{ $photoUrl }}" data-lightbox="gallery" data-title="Фото {{ $key + 1 }}">
                    <img src="{{ $photoUrl }}"
                        class="side-photo shadow-sm rounded"
                        alt="Фото помешкання">
                </a>
                @if($key == 3 && $accommodation->photos->count() > 5)
                <button class="view-all-btn btn btn-outline-primary" onclick="openGalleryModal()">
                    Переглянути всі фото
                </button>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Модальне вікно для перегляду всіх фото --}}
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Галерея фото</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="photo-grid">
                        @foreach($accommodation->photos as $photo)
                        @php
                        // Визначаємо, чи шлях згенерований Laravel Storage
                        $isStoragePath = Str::startsWith($photo->photo_path, ['accommodation_photos/', 'photos/']);
                        $photoUrl = $isStoragePath ? asset('storage/' . $photo->photo_path) : asset($photo->photo_path);
                        @endphp
                        <div class="photo-item">
                            <a href="{{ $photoUrl }}" data-lightbox="gallery" data-title="Фото {{ $loop->iteration }}">
                                <img src="{{ $photoUrl }}" class="rounded" alt="Фото помешкання">
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Секція з описом і бронюванням -->
    <div class="d-flex justify-content-between w-80 mt-4">
        {{-- Секція з описом помешкання --}}
        <div class="description-section w-50">
            <h4>{{ $accommodation->name }}</h4>
            <div class="rating mb-2">
                <span class="average-rating">{{ number_format($accommodation->averageRating(), 1) }}</span>
                <span class="stars">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= round($accommodation->averageRating()) ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                </span>
                <span class="reviews-count">({{ $accommodation->reviewsCount() }} відгуків)</span>
            </div>
            <p class="text-muted">
                <i class="fas fa-map-marker-alt"></i>
                {{ $accommodation->settlement }}, {{ $accommodation->region }}
            </p>
            <div class="mt-3 text-start">
                <p class="detailed-description">
                    {{ Str::limit($accommodation->detailed_description, 250, '...') }}
                    <span class="expand-description text-primary" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#fullDescriptionModal">
                        Читати більше
                    </span>
                </p>
            </div>
            @if ($accommodation->mealOptions->isNotEmpty())
            <div class="mt-4">
                <h4 class="meal-title">Доступні варіанти харчування:</h4>
                <div class="meal-list">
                    @foreach ($accommodation->mealOptions as $meal)
                    <div class="meal-item">
                        <span class="meal-name">{{ $meal->name }}</span>
                        <span class="meal-price">
                            @if(isset($meal->pivot->price))
                            {{ $meal->pivot->price }} грн
                            @else
                            Ціна не вказана
                            @endif
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="amenities-container">
                @php
                $groupedAmenities = $accommodation->amenities->groupBy('category.category_name');
                @endphp
                @foreach($groupedAmenities as $categoryName => $amenities)
                <div class="amenities-column">
                    <div class="category">
                        @php
                        $iconPath = public_path('icons/' . ($categoryName ?? 'default') . '.png');
                        @endphp
                        @if(file_exists($iconPath))
                        <img src="{{ asset('icons/' . ($categoryName ?? 'default') . '.png') }}" alt="Іконка категорії" class="category-icon">
                        @endif
                        {{ $categoryName ?? 'Без категорії' }}
                    </div>
                    <ul class="amenities-list">
                        @foreach($amenities as $amenity)
                        <li>{{ $amenity->name }}</li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>

            <p><strong>Зверніть увагу</strong></p>
            <div class="additional-info">
                <p><strong>Діти:</strong> {{ $accommodation->children ?? 'Не вказано' }}</p>
                <p><strong>Ліжка:</strong> {{ $accommodation->beds ?? 'Не вказано' }}</p>
                <p><strong>Обмеження за віком:</strong> {{ $accommodation->age_restrictions ?? 'Не вказано' }}</p>
                <p><strong>Дозволено з тваринами:</strong> {{ $accommodation->pets_allowed ?? 'Не вказано' }}</p>
                <p><strong>Способи оплати:</strong> {{ $accommodation->payment_options ?? 'Не вказано' }}</p>
                <p><strong>Дозволено вечірки:</strong> {{ $accommodation->parties_allowed ?? 'Не вказано' }}</p>
                <p><strong>Час заїзду:</strong> {{ $accommodation->checkin_time ?? 'Не вказано' }}</p>
                <p><strong>Час виїзду:</strong> {{ $accommodation->checkout_time ?? 'Не вказано' }}</p>
            </div>

            <div class="cancellation-policy mt-4 p-4 rounded shadow-sm bg-light">
                <h5 class="fw-bold text-primary">Політика скасування</h5>
                <p class="text-muted mb-2">
                    {!! nl2br(e($accommodation->cancellation_policy ?? 'Політика скасування не вказана')) !!}
                </p>
            </div>

            <!-- Секція відгуків -->
            <div class="reviews-section mt-4 p-4 rounded shadow-sm bg-light">
                <h5 class="fw-bold text-primary">Відгуки</h5>
                @if($accommodation->reviews->isNotEmpty())
                <div class="reviews-list">
                    @foreach($accommodation->reviews as $review)
                    <div class="review-item mb-3 p-3 border rounded bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $review->user->name }}</strong>
                                <span class="text-muted ms-2">{{ $review->created_at->format('d.m.Y') }}</span>
                            </div>
                            <div class="review-rating">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                            </div>
                        </div>
                        <p class="mt-2">{{ $review->comment }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-muted">Поки що немає відгуків. Будьте першим, хто залишить відгук!</p>
                @endif
            </div>

           <!-- Форма для додавання відгуку -->
@auth
<div class="add-review-section mt-4">
    <h6 class="fw-bold">Залишити відгук</h6>
    <form action="{{ route('reviews.store', ['accommodationId' => $accommodation->id]) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="rating" class="form-label">Ваш рейтинг:</label>
            <select name="rating" id="rating" class="form-select w-auto" required>
                @for ($i = 1; $i <= 5; $i++)
                    <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'зірка' : 'зірок' }}</option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label for="comment" class="form-label">Ваш відгук:</label>
            <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Надіслати відгук</button>
    </form>
</div>
@endauth
        </div>

        <!-- Секція з ціною та датами (з картою) -->
        <div class="booking-section d-flex flex-column justify-content-start align-items-center p-4 rounded-3 shadow"
            data-booked-dates="@json($accommodation->bookedDates->map(function($date) {
                 return [
                     'start' => $date->start_date->format('Y-m-d'),
                     'end' => $date->end_date->format('Y-m-d')
                 ];
             }) ?? [])">
            <p class="price text-dark fs-3">
                <strong id="total-price">{{ $accommodation->price_per_night }} грн/ніч</strong>
            </p>
            <div class="booking-form w-100 d-flex flex-column align-items-center">
                <div class="w-100 booking-details">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex flex-column w-38">
                            <label for="checkin" class="form-label fw-bold nl">Прибуття</label>
                            <input type="text" id="checkin" class="form-control rounded" placeholder="Виберіть дату" required />
                        </div>
                        <div class="d-flex flex-column w-48">
                            <label for="checkout" class="form-label fw-bold">Виїзд</label>
                            <input type="text" id="checkout" class="form-control rounded" placeholder="Виберіть дату" required />
                        </div>
                    </div>

                    <!-- Блок для вибору гостей -->
                    <div class="w-100 position-relative mt-3">
                        <label class="form-label fw-bold">Гості</label>
                        <div class="guest-dropdown-toggle bg-white p-3 rounded border d-flex justify-content-between align-items-center" onclick="toggleGuestDropdown()">
                            <span id="guest-summary">1 дорослий</span>
                            <span id="dropdown-arrow" class="arrow">▾</span>
                        </div>
                        <div class="guest-selection bg-white p-3 rounded border position-absolute w-100 shadow" id="guest-dropdown" style="display: none;">
                            @foreach(['adults' => 'Дорослі', 'children' => 'Діти', 'infants' => 'Немовлята', 'pets' => 'Тварини'] as $type => $label)
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span>{{ $label }}</span>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('{{ $type }}', -1)">-</button>
                                    <span id="{{ $type }}-count" class="mx-2">{{ $type == 'adults' ? 1 : 0 }}</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('{{ $type }}', 1)">+</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Харчування -->
                    @if ($accommodation->mealOptions->isNotEmpty())
                    <div class="mt-3">
                        <label class="form-label fw-bold">Харчування</label>
                        <div id="meal_option">
                            @foreach ($accommodation->mealOptions as $meal)
                            <div class="form-group mb-2">
                                <label for="meal_option_{{ $meal->id }}">
                                    {{ $meal->name }}
                                    @if(isset($meal->pivot->price))
                                    (+{{ $meal->pivot->price }} грн за гостя)
                                    @else
                                    (Ціна не вказана)
                                    @endif
                                </label>
                                <select class="form-control meal-select" id="meal_option_{{ $meal->id }}" name="meal_option[{{ $meal->id }}]" data-price="{{ $meal->pivot->price ?? 0 }}" onchange="updateTotalPrice()">
                                    <option value="0">Не вибрано</option>
                                    @for($i = 1; $i <= 5; $i++)
                                        <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'гість' : ($i < 5 ? 'гостя' : 'гостей') }}</option>
                                        @endfor
                                </select>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Відображення зайнятих дат -->
                    @if($accommodation->bookedDates->isNotEmpty())
                    <div class="mt-3">
                        <label class="form-label fw-bold">Зайняті дати:</label>
                        <ul class="booked-dates-list">
                            @foreach($accommodation->bookedDates->sortBy('start_date') as $bookedDate)
                            <li>{{ $bookedDate->start_date->format('d.m.Y') }} - {{ $bookedDate->end_date->format('d.m.Y') }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <div class="mt-3">
                        <p class="text-muted">Наразі немає зайнятих дат.</p>
                    </div>
                    @endif

                    <!-- Карта через Leaflet -->
                    <div id="map" style="height: 200px; width: 100%; margin-top: 20px;"
                        data-lat="{{ $accommodation->latitude ?? 50.4501 }}"
                        data-lng="{{ $accommodation->longitude ?? 30.5234 }}"
                        data-name="{{ $accommodation->name }}"
                        data-location="{{ $accommodation->settlement }}, {{ $accommodation->region }}">
                    </div>
                    <!-- Посилання для перегляду розташування -->
                    <div class="mt-2 text-center">
                        <a href="https://www.google.com/maps?q={{ $accommodation->latitude ?? 50.4501 }},{{ $accommodation->longitude ?? 30.5234 }}"
                            target="_blank"
                            class="text-primary text-decoration-none">
                            Переглянути розташування
                        </a>
                    </div>

                    <button class="btn btn-success w-75 mt-3 rounded-3 add-to-cart-btn"
                        data-id="{{ $accommodation->id }}"
                        data-price="{{ $accommodation->price_per_night }}"
                        data-photo="{{ $mainPhoto->photo_path ?? '' }}"
                        data-total="">
                        Додати в кошик
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальне вікно для повного опису -->
    <div class="modal fade" id="fullDescriptionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Повний опис</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="fullDescriptionText">{{ $accommodation->detailed_description }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Підключення бібліотек -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
    <!-- Підключення Leaflet через CDN -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Підключення Flatpickr через CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Ініціалізація карти -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const mapEl = document.getElementById("map");
            if (mapEl) {
                const lat = parseFloat(mapEl.dataset.lat);
                const lng = parseFloat(mapEl.dataset.lng);
                const name = mapEl.dataset.name;
                const location = mapEl.dataset.location;

                const map = L.map('map').setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                L.marker([lat, lng]).addTo(map)
                    .bindPopup(`${name}<br>${location}`)
                    .openPopup();
            }

            // Тимчасово закодовані дати для тестування
            const bookedDates = [{
                    start: "2025-11-08",
                    end: "2025-11-11"
                },
                {
                    start: "2025-12-22",
                    end: "2025-12-28"
                },
                {
                    start: "2025-06-03",
                    end: "2025-06-10"
                },
                {
                    start: "2025-09-16",
                    end: "2025-09-22"
                },
                {
                    start: "2025-11-15",
                    end: "2025-11-21"
                }
            ];

            // Функція для створення масиву всіх зайнятих дат
            function getDisabledDates(bookedDates) {
                let disabledDates = [];
                bookedDates.forEach(period => {
                    let start = new Date(period.start);
                    let end = new Date(period.end);
                    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
                        disabledDates.push(new Date(d).toISOString().split('T')[0]);
                    }
                });
                return disabledDates;
            }

            const disabledDates = getDisabledDates(bookedDates);

            // Ініціалізація Flatpickr з урахуванням зайнятих дат
            const checkinPicker = flatpickr("#checkin", {
                dateFormat: "Y-m-d",
                minDate: "today",
                disable: disabledDates,
                onChange: function(selectedDates, dateStr) {
                    checkoutPicker.set('minDate', dateStr);
                    updateTotalPrice();
                }
            });

            const checkoutPicker = flatpickr("#checkout", {
                dateFormat: "Y-m-d",
                minDate: "today",
                disable: disabledDates,
                onChange: function(selectedDates, dateStr) {
                    checkinPicker.set('maxDate', dateStr);
                    updateTotalPrice();
                }
            });

            // Логіка для гостей
            let dropdownOpen = false;

            window.toggleGuestDropdown = function() {
                let dropdown = document.getElementById("guest-dropdown");
                let arrow = document.getElementById("dropdown-arrow");
                dropdownOpen = !dropdownOpen;
                dropdown.style.display = dropdownOpen ? "block" : "none";
                arrow.classList.toggle("open", dropdownOpen);
                if (!dropdownOpen) updateGuestSummary();
            };

            window.changeGuestCount = function(type, change) {
                let countElement = document.getElementById(`${type}-count`);
                let currentCount = parseInt(countElement.textContent);
                if (currentCount + change >= 0) {
                    countElement.textContent = currentCount + change;
                }
            };

            function updateGuestSummary() {
                let adults = parseInt(document.getElementById("adults-count").textContent);
                let children = parseInt(document.getElementById("children-count").textContent);
                let infants = parseInt(document.getElementById("infants-count").textContent);
                let pets = parseInt(document.getElementById("pets-count").textContent);
                let summary = [];
                if (adults > 0) summary.push(`${adults} доросл${adults > 1 ? 'і' : 'ий'}`);
                if (children > 0) summary.push(`${children} ${children > 1 ? 'дітей' : 'дитина'}`);
                if (infants > 0) summary.push(`${infants} немовля${infants > 1 ? 'т' : ''}`);
                if (pets > 0) summary.push(`${pets} ${pets > 1 ? 'тварин' : 'тварина'}`);
                document.getElementById("guest-summary").textContent = summary.length > 0 ? summary.join(", ") : "1 дорослий";
            }

            // Оновлення ціни
            let basePrice = parseFloat(document.getElementById("total-price").textContent.replace(" грн/ніч", ""));

            function calculateNights() {
                let checkinDate = document.getElementById("checkin").value;
                let checkoutDate = document.getElementById("checkout").value;
                if (checkinDate && checkoutDate) {
                    let checkin = new Date(checkinDate);
                    let checkout = new Date(checkoutDate);
                    let timeDifference = checkout - checkin;
                    let nights = timeDifference / (1000 * 3600 * 24);
                    return nights > 0 ? nights : 0;
                }
                return 0;
            }

            window.updateTotalPrice = function() {
                let nights = calculateNights();
                let totalPrice = basePrice * (nights > 0 ? nights : 1);
                document.querySelectorAll(".meal-select").forEach(select => {
                    let selected = parseInt(select.value);
                    let mealPrice = parseFloat(select.getAttribute("data-price")) || 0;
                    if (selected > 0) {
                        totalPrice += selected * mealPrice;
                    }
                });
                document.getElementById("total-price").textContent = totalPrice + " грн";
                const addToCartBtn = document.querySelector(".add-to-cart-btn");
                if (addToCartBtn) {
                    addToCartBtn.setAttribute("data-total", totalPrice);
                }
            };

            // Додавання слухачів для оновлення ціни
            document.getElementById("checkin").addEventListener("change", updateTotalPrice);
            document.getElementById("checkout").addEventListener("change", updateTotalPrice);
            document.querySelectorAll(".meal-select").forEach(select => {
                select.addEventListener("change", updateTotalPrice);
            });

            // Додавання в кошик
            const addToCartBtn = document.querySelector(".add-to-cart-btn");
            if (addToCartBtn) {
                addToCartBtn.addEventListener("click", function() {
                    let checkinDate = document.getElementById("checkin").value;
                    let checkoutDate = document.getElementById("checkout").value;

                    // Перевірка, чи вибрані дати
                    if (!checkinDate || !checkoutDate) {
                        showToast('Будь ласка, виберіть дати заїзду та виїзду', 'error');
                        return;
                    }

                    // Перевірка, чи вибрані дати перетинаються із зайнятими
                    let selectedStart = new Date(checkinDate);
                    let selectedEnd = new Date(checkoutDate);
                    let overlap = bookedDates.some(period => {
                        let bookedStart = new Date(period.start);
                        let bookedEnd = new Date(period.end);
                        return (selectedStart <= bookedEnd && selectedEnd >= bookedStart);
                    });

                    if (overlap) {
                        showToast('Вибрані дати перетинаються із зайнятими. Виберіть інші дати.', 'error');
                        return;
                    }

                    let cartData = {
                        accommodation_id: this.getAttribute("data-id"),
                        price: this.getAttribute("data-price"),
                        photo: this.getAttribute("data-photo"),
                        total_price: this.getAttribute("data-total"),
                        checkin_date: checkinDate,
                        checkout_date: checkoutDate,
                        guests_count: {
                            adults: parseInt(document.getElementById("adults-count").textContent),
                            children: parseInt(document.getElementById("children-count").textContent),
                            infants: parseInt(document.getElementById("infants-count").textContent),
                            pets: parseInt(document.getElementById("pets-count").textContent)
                        },
                        meal_options: []
                    };
                    document.querySelectorAll(".meal-select").forEach(select => {
                        let selectedOption = parseInt(select.value);
                        if (selectedOption > 0) {
                            let mealId = select.id.split('_').pop();
                            cartData.meal_options.push({
                                meal_option_id: mealId,
                                guests_count: selectedOption
                            });
                        }
                    });

                    fetch('/cart/add', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify(cartData)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showToast('Товар успішно додано до кошика!', 'success');
                                const cartBadge = document.querySelector('.cart .badge');
                                if (cartBadge) {
                                    cartBadge.textContent = data.cart_items_count;
                                }
                            } else {
                                showToast('Помилка при додаванні до кошика', 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Помилка:', error);
                            showToast('Сталася помилка', 'error');
                        });
                });
            }

            // Ініціалізація Swiper
            var swiper = new Swiper(".mySwiper", {
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                loop: true,
                keyboard: true,
            });

            window.openGalleryModal = function() {
                var galleryModal = new bootstrap.Modal(document.getElementById('galleryModal'));
                galleryModal.show();
            };
        });

        function showToast(message, type = 'success') {
            const toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.style = 'position: fixed; top: 20px; right: 20px; z-index: 1000;';
            document.body.appendChild(toastContainer);
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            toast.innerHTML = `<span>${message}</span>`;
            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };
    </script>

    <style>
        .booking-section a:hover {
            text-decoration: underline;
            color: #0056b3;
        }

        #map {
            height: 200px;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-top: 20px;
        }

        .amenities-container {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .category {
            font-size: 14px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 5px;
        }

        .amenities-list {
            list-style: none;
            padding: 0;
            font-size: 12px;
        }

        .amenities-list li {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .amenities-list li::before {
            content: "✓";
            font-weight: bold;
            color: black;
        }

        .category-icon {
            width: 16px;
            height: 16px;
        }

        .additional-info {
            font-size: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 20px;
            background: #fafafa;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.05);
        }

        .additional-info p {
            margin: 6px 0;
            color: #333;
        }

        .additional-info strong {
            color: #555;
        }

        .amenities-section {
            margin-top: 10px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
        }

        .amenity-item {
            background: white;
            display: flex;
            align-items: center;
            font-size: 14px;
            font-weight: 500;
        }

        .amenity-item i {
            font-size: 18px;
        }

        .guest-dropdown-toggle {
            background: #fff;
            cursor: pointer;
            font-size: 14px;
        }

        .guest-selection {
            display: none;
            font-size: 14px;
        }

        .guest-option {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
        }

        .counter {
            display: flex;
            align-items: center;
        }

        .counter button {
            width: 24px;
            height: 24px;
            font-size: 14px;
        }

        .count {
            min-width: 24px;
            text-align: center;
        }

        .meal-selection {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .arrow {
            transition: transform 0.2s ease;
        }

        .arrow.open {
            transform: rotate(180deg);
        }

        .photo-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
            justify-content: center;
            align-items: center;
        }

        .photo-item img:hover {
            transform: scale(1.05);
        }

        .photo-gallery {
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .main-photo-wrapper {
            width: 870px;
            height: 380px;
            display: flex;
            justify-content: center;
        }

        .main-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s;
            margin-left: 60px;
        }

        .side-photos {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
        }

        .photo-wrapper {
            width: 290px;
            height: 185px;
            position: relative;
        }

        .side-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.2s;
        }

        .main-photo:hover,
        .side-photo:hover {
            transform: scale(1.05);
        }

        .view-all-btn {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.6);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 14px;
        }

        .view-all-btn:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        .booking-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 450px;
            min-height: 650px;
            margin-right: 60px;
        }

        .booking-details {
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            gap: 10px;
        }

        .form-label {
            font-weight: bold;
        }

        .form-select,
        .form-control {
            width: 100%;
            max-width: 250px;
        }

        .btn-success {
            padding: 12px 20px;
            font-size: 18px;
        }

        .description-section {
            width: 50%;
            padding: 15px;
            margin-left: 50px;
        }

        .detailed-description {
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .detailed-description.expanded {
            max-height: none;
        }

        .description-section h4 {
            margin-bottom: 10px;
        }

        .meal-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .meal-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .meal-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            padding: 8px 12px;
            border-radius: 6px;
            background: #f8f9fa;
        }

        .meal-name {
            font-weight: 500;
        }

        .meal-price {
            font-weight: 600;
            color: #28a745;
        }

        .toast-notification {
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease-in-out;
        }

        .toast-notification.error {
            background: #dc3545;
        }

        .toast-notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        .booked-dates-list {
            list-style: none;
            padding: 0;
            font-size: 14px;
            color: #dc3545;
        }

        .booked-dates-list li {
            margin-bottom: 5px;
        }

        .flatpickr-day.disabled {
            background-color: #ffcccc !important;
            color: #666 !important;
            cursor: not-allowed !important;
        }

        .flatpickr-day.disabled:hover {
            background-color: #ff9999 !important;
        }

        .reviews-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .review-item {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 10px;
        }

        .review-rating .fas.fa-star {
            font-size: 14px;
        }

        .average-rating {
            font-size: 18px;
            font-weight: bold;
            margin-right: 5px;
        }

        .stars .fas.fa-star {
            font-size: 16px;
        }

        .reviews-count {
            font-size: 14px;
            color: #666;
            margin-left: 5px;
        }

        .add-review-section textarea {
            resize: none;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".expand-description").forEach(button => {
                button.addEventListener("click", function() {
                    let description = this.closest(".detailed-description");
                    let moreText = description.querySelector(".more-text");
                    if (description.dataset.expanded === "false") {
                        moreText.style.display = "inline";
                        this.textContent = "Згорнути";
                        description.dataset.expanded = "true";
                    } else {
                        moreText.style.display = "none";
                        this.textContent = "Читати більше";
                        description.dataset.expanded = "false";
                    }
                });
            });
        });
    </script>

    @endsection
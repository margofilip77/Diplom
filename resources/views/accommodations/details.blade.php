@extends('layouts.app')

@section('content')
<div class="container mt-4 d-flex flex-column align-items-center">

    {{-- Галерея фото --}}
    <div class="photo-gallery d-flex">
        {{-- Головне фото --}}
        @if($accommodation->photos->isNotEmpty())
        @php
        $mainPhoto = $accommodation->photos->first();
        @endphp
        <div class="main-photo-wrapper">
            <a href="{{ asset($mainPhoto->photo_path) }}" data-lightbox="gallery" data-title="Головне фото помешкання">
                <img src="{{ asset($mainPhoto->photo_path) }}"
                    class="main-photo shadow-lg rounded"
                    alt="Головне фото помешкання">
            </a>
        </div>
        @endif

        {{-- Блок з додатковими фото з правого боку --}}
        <div class="side-photos d-flex flex-wrap ms-3">
            @foreach($accommodation->photos->skip(1)->take(4) as $key => $photo)
            <div class="photo-wrapper position-relative">
                <a href="{{ asset($photo->photo_path) }}" data-lightbox="gallery" data-title="Фото {{ $key + 1 }}">
                    <img src="{{ asset($photo->photo_path) }}"
                        class="side-photo shadow-sm rounded"
                        alt="Фото помешкання">
                </a>

                {{-- Кнопка "Переглянути всі фото" на останньому фото --}}
                @if($key == 4 && $accommodation->photos->count() > 5)
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
                        <div class="photo-item">
                            <a href="{{ asset($photo->photo_path) }}" data-lightbox="gallery" data-title="Фото {{ $loop->iteration }}">
                                <img src="{{ asset($photo->photo_path) }}" class="rounded" alt="Фото помешкання">
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Секція з ціною, датами і кількістю гостей -->
    <div class="d-flex justify-content-between w-80 mt-4">
        {{-- Секція з описом помешкання --}}
        <div class="description-section w-50">
            <h4>{{ $accommodation->name }}</h4>
            <p class="text-muted">
                <i class="fas fa-map-marker-alt"></i>
                {{ $accommodation->settlement }}, {{ $accommodation->region }}
            </p>
            {{-- Короткий опис --}}
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
                    <span class="meal-price">{{ $meal->pivot->price }} грн</span>
                </div>
            @endforeach
        </div>
    </div>
@endif


            <style>
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
            </style>

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

            <style>
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
            </style>


            <div class="cancellation-policy mt-4 p-4 rounded shadow-sm bg-light">
                <h5 class="fw-bold text-primary">Політика скасування</h5>
                <p class="text-muted mb-2">
                    {!! nl2br(e($accommodation->cancellation_policy ?? 'Політика скасування не вказана')) !!}
                </p>
            </div>

        </div>
        {{-- Модальне вікно для повного опису --}}
        <div class="modal fade" id="fullDescriptionModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Повний опис</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p id="fullDescriptionText">
                            {{ $accommodation->detailed_description }}
                        </p>
                    </div>
                </div>
            </div>
        </div>


        <!-- Секція з ціною та датами -->
        <div class="booking-section d-flex flex-column justify-content-center align-items-center  p-4 rounded-3 shadow">
            <div class="price-section text-center ">
                <p class="price text-dark fs-3"><strong>{{ $accommodation->price_per_night }} грн/ніч</strong></p>
            </div>
            <div class="booking-form w-100 d-flex flex-column align-items-center">
                <div class="w-100 booking-details">
                    <div class="d-flex justify-content-between ">
                        <div class="d-flex flex-column w-38">
                            <label for="checkin" class="form-label fw-bold nl">Прибуття</label>
                            <input type="text" id="checkin" class="form-control rounded" placeholder="Виберіть дату" />
                        </div>
                        <div class="d-flex flex-column w-48">
                            <label for="checkout" class="form-label fw-bold">Виїзд</label>
                            <input type="text" id="checkout" class="form-control rounded" placeholder="Виберіть дату" />
                        </div>
                    </div>

                    <!-- Блок для вибору гостей -->
                    <div class="w-100 position-relative">
                        <label class="form-label fw-bold">Гості</label>
                        <div class="guest-dropdown-toggle bg-white p-3 rounded border d-flex justify-content-between align-items-center"
                            onclick="toggleGuestDropdown()">
                            <span id="guest-summary">1 дорослий</span>
                            <span id="dropdown-arrow" class="arrow">&#9662;</span> <!-- ▼ значок стрілки -->
                        </div>

                        <div class="guest-selection bg-white p-3 rounded border position-absolute w-100 shadow"
                            id="guest-dropdown" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Дорослі</span>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('adults', -1)">-</button>
                                    <span id="adults-count" class="mx-2">1</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('adults', 1)">+</button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span>Діти</span>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('children', -1)">-</button>
                                    <span id="children-count" class="mx-2">0</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('children', 1)">+</button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span>Немовлята</span>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('infants', -1)">-</button>
                                    <span id="infants-count" class="mx-2">0</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('infants', 1)">+</button>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span>Тварини</span>
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('pets', -1)">-</button>
                                    <span id="pets-count" class="mx-2">0</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="changeGuestCount('pets', 1)">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($accommodation->mealOptions->isNotEmpty())
                    <div class="mt-3"><label class="form-label fw-bold">Харчування</label>
                        <div id="meal_option">
                            @foreach ($accommodation->mealOptions as $meal)
                            <div class="form-group mb-2">
                                <label for="meal_option_{{ $meal->id }}">{{ $meal->name }} (+{{ $meal->pivot->price }} грн за гостя)</label>
                                <select class="form-control meal-select" id="meal_option_{{ $meal->id }}" data-price="{{ $meal->pivot->price }}" onchange="updateTotalPrice()">
                                    <option value="0">Не вибрано</option>
                                    <option value="1">1 гість</option>
                                    <option value="2">2 гостя</option>
                                    <option value="3">3 гостя</option>
                                    <option value="4">4 гостя</option>
                                    <option value="5">5 гостей</option>
                                </select>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <button class="btn btn-success w-75 mt-3 rounded-3 add-to-cart-btn" data-id="{{ $accommodation->id }}" data-price="{{ $accommodation->price_per_night }}" data-photo="{{ $accommodation->photo }}">
    Додати в кошик
</button>

        </div>
        <!-- Секція для підсумкової вартості -->
        <div id="accommodation-price" data-price="{{ $accommodation->price_per_night }}"></div>


        <script>
            let dropdownOpen = false; // Перемінна для перевірки стану списку

            function toggleGuestDropdown() {
                let dropdown = document.getElementById("guest-dropdown");
                let arrow = document.getElementById("dropdown-arrow");

                // Визначаємо, чи список відкритий
                dropdownOpen = !dropdownOpen;
                dropdown.style.display = dropdownOpen ? "block" : "none";
                arrow.classList.toggle("open", dropdownOpen);

                // Якщо закрили список, оновлюємо текст
                if (!dropdownOpen) {
                    updateGuestSummary();
                }
            }

            function changeGuestCount(type, change) {
                let countElement = document.getElementById(`${type}-count`);
                let currentCount = parseInt(countElement.textContent);

                if (currentCount + change >= 0) {
                    countElement.textContent = currentCount + change;
                }
            }

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

                // Оновлюємо текст у верхній комірці
                document.getElementById("guest-summary").textContent = summary.length > 0 ? summary.join(", ") : "1 дорослий";
            }

            // Закриття списку гостей при натисканні поза ним
            document.addEventListener("click", function(event) {
                let dropdown = document.getElementById("guest-dropdown");
                let toggle = document.querySelector(".guest-dropdown-toggle");
                let arrow = document.getElementById("dropdown-arrow");

                if (!dropdown.contains(event.target) && !toggle.contains(event.target)) {
                    dropdown.style.display = "none";
                    arrow.classList.remove("open");
                    if (dropdownOpen) {
                        updateGuestSummary(); // Оновлення тексту після закриття
                    }
                    dropdownOpen = false;
                }
            });
        </script>

        <script>
          document.querySelectorAll('.add-to-cart-btn').forEach(button => {
    button.addEventListener('click', function() {
        let accommodationId = this.getAttribute('data-id');
        let accommodationPhoto = this.getAttribute('data-photo');
        let checkinDate = document.getElementById('checkin').value;
        let checkoutDate = document.getElementById('checkout').value;
        let adults = document.getElementById("adults-count").textContent;
        let children = document.getElementById("children-count").textContent;
        let infants = document.getElementById("infants-count").textContent;
        let pets = document.getElementById("pets-count").textContent;
        let meals = [];
        document.querySelectorAll(".meal-select").forEach(select => {
            let mealCount = select.value;
            if (mealCount > 0) {
                meals.push({
                    id: select.id,
                    guests: mealCount
                });
            }
        });

        // Додаємо все у кошик
        addToCart(accommodationId, accommodationPhoto, checkinDate, checkoutDate, adults, children, infants, pets, meals);
    });
});

function addToCart(accommodationId, accommodationPhoto, checkinDate, checkoutDate, adults, children, infants, pets, meals) {
    fetch('{{ route("cart.add") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            accommodation_id: accommodationId,
            accommodation_photo: accommodationPhoto, // Додаємо фото
            checkin: checkinDate,
            checkout: checkoutDate,
            adults: adults,
            children: children,
            infants: infants,
            pets: pets,
            meals: meals
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.message) {
            alert(data.message); // Показати повідомлення про додавання
        }
    })
    .catch(error => console.error('Помилка:', error));
}
        </script>
        <script>
            function updateTotalPrice() {
                const accommodationPrice = parseFloat(document.getElementById('accommodation-price').getAttribute('data-price'));

                // Отримуємо кількість ночей
                const checkinDate = new Date(document.getElementById('checkin').value);
                const checkoutDate = new Date(document.getElementById('checkout').value);
                const nights = (checkoutDate - checkinDate) / (1000 * 60 * 60 * 24) || 1;

                // Отримуємо кількість гостей
                const adults = parseInt(document.getElementById("adults-count").textContent);
                const children = parseInt(document.getElementById("children-count").textContent);

                // Підрахунок вартості харчування
                let totalMealPrice = 0;
                document.querySelectorAll(".meal-select").forEach(select => {
                    const mealGuests = parseInt(select.value);
                    const mealPrice = parseFloat(select.getAttribute("data-price")) || 0;
                    totalMealPrice += mealGuests * mealPrice * nights;
                });

                // Загальна сума
                const totalPrice = (accommodationPrice * nights) + totalMealPrice;
                document.querySelector('.price-section .price').innerHTML = `<strong>${totalPrice} грн</strong>`;
            }
            document.addEventListener("DOMContentLoaded", function() {
    document.querySelector(".add-to-cart-btn").addEventListener("click", function() {
        const accommodationId = this.getAttribute("data-id");

        // Отримуємо всі дані
        const accommodationPrice = parseFloat(document.getElementById("accommodation-price").getAttribute("data-price"));
        const checkinDate = document.getElementById("checkin").value;
        const checkoutDate = document.getElementById("checkout").value;
        const nights = (new Date(checkoutDate) - new Date(checkinDate)) / (1000 * 60 * 60 * 24) || 1;

        const adults = parseInt(document.getElementById("adults-count").textContent);
        const children = parseInt(document.getElementById("children-count").textContent);
        const infants = parseInt(document.getElementById("infants-count").textContent);
        const pets = parseInt(document.getElementById("pets-count").textContent);
        const totalGuests = adults + children; // Немовлята та тварини не враховуються у харчуванні

        // Отримуємо вибране харчування
        const selectedMeals = [];
        let totalMealPrice = 0;
        let totalSelectedMealsGuests = 0;

        document.querySelectorAll(".meal-select").forEach(select => {
            const mealGuests = parseInt(select.value);
            const mealPrice = parseFloat(select.getAttribute("data-price")) || 0;
            const mealName = select.closest(".form-group").querySelector("label").innerText.split(" (")[0]; // Коректно отримуємо назву

            if (mealGuests > 0) {
                selectedMeals.push({
                    name: mealName,
                    guests: mealGuests,
                    price: mealPrice
                });
                totalMealPrice += mealGuests * mealPrice * nights;
                totalSelectedMealsGuests += mealGuests;
            }
        });

        // Перевіряємо, чи кількість гостей у харчуванні не перевищує загальну кількість дорослих та дітей
        if (totalSelectedMealsGuests > totalGuests) {
            alert("Кількість гостей у харчуванні не може перевищувати загальну кількість дорослих та дітей.");
            return;
        }

        // Підрахунок загальної вартості
        const totalPrice = (accommodationPrice * nights) + totalMealPrice;

        // Об'єкт для збереження у кошик
        const cartItem = {
            id: accommodationId,
            price: totalPrice,
            checkin: checkinDate,
            checkout: checkoutDate,
            guests: {
                adults,
                children,
                infants,
                pets
            },
            meals: selectedMeals
        };

        // Оновлення кошика в localStorage
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        cart.push(cartItem);
        localStorage.setItem("cart", JSON.stringify(cart));

        alert("Товар додано до кошика!");
    });
});

           

            document.querySelectorAll(".meal-select").forEach(select => {
                select.addEventListener("change", updateTotalPrice);
            });

            document.getElementById("checkin").addEventListener("change", updateTotalPrice);
            document.getElementById("checkout").addEventListener("change", updateTotalPrice);
        </script>


    </div>
</div>

</div>

{{-- Підключення календаря для вибору дат --}}
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Підключення Bootstrap JavaScript (якщо ще не підключений) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    flatpickr("#checkin", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });
    flatpickr("#checkout", {
        dateFormat: "Y-m-d",
        minDate: "today"
    });

    function changeGuestCount(type, change) {
        let countElement = document.getElementById(`${type}-count`);
        let currentCount = parseInt(countElement.textContent);

        if (currentCount + change >= 0) {
            countElement.textContent = currentCount + change;
        }
    }
</script>

{{-- Інформація про помешкання --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<!-- Lightbox CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
<!-- Lightbox JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

{{-- Стилі --}}
<style>
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

    /* Стиль стрілки */
    .arrow {
        transition: transform 0.2s ease;
    }

    .arrow.open {
        transform: rotate(180deg);
        /* Повертає стрілку вгору */
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
        height: 650px;
        margin-right: 60px;
    }

    .booking-details {
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        gap: 10px;
    }

    .price-section {
        flex: 1;
    }

    .booking-form {
        flex: 2;
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

</style>

{{-- JavaScript для відкриття модального вікна --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var swiper = new Swiper(".mySwiper", {
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            loop: true,
            keyboard: true,
        });
    });

    function openGalleryModal() {
        var galleryModal = new bootstrap.Modal(document.getElementById('galleryModal'));
        galleryModal.show();
    }
</script>
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
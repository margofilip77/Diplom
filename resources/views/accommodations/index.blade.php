@extends('layouts.app')

<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        height: 200px;
        /* Трохи збільшена для додавання опису */
        border: none;
    }
    a {
    text-decoration: none; /* Прибирає підкреслення */
    color: inherit; /* Робить колір тексту стандартним */
}

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .card img {
        height: 150px;
        /* Менша висота фото */

        object-fit: cover;
        border-radius: 15px 15px 0 0;
    }

    .description {
        font-size: 14px;
        color: #555;
        max-height: 38px;
        /* Два рядки */
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .card-title {
        font-size: 18px;
        /* Трохи менший заголовок */
        font-weight: bold;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 10px;
        /* Зменшений відступ */
    }

    .price {
        font-size: 18px;
        font-weight: bold;
        color: #28a745;
        /* Виділена зелена ціна */
    }

    .icon {
        vertical-align: middle;
        margin-right: 5px;
        color: #28a745;
    }

    .btn-green {
        background-color: #28a745;
        border: none;
    }

    .btn-green:hover {
        background-color: #218838;
    }

    .like-button {
        background: rgba(0, 0, 0, 0.4);
        border: none;
        padding: 8px 12px;
        border-radius: 50px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .like-button:hover {
        background: rgba(0, 0, 0, 0.6);
    }

    .like-icon {
        font-size: 24px;
        vertical-align: middle;
    }

    .text-warning {
        color: #ffc107 !important;
        /* Жовтий для рейтингу */
    }

    .search-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .search-icon {
        background: #28a745;
        /* Зелений колір */
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        border-radius: 50%;
    }

    .search-icon span {
        font-size: 28px;
        color: #fff;
    }

    .search-title {
        font-size: 28px;
        font-weight: bold;
        margin-top: 10px;
    }

    .search-subtitle {
        font-size: 16px;
        color: #777;
    }

    .search-form-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        /* Мінімальна висота 100% від висоти екрану */
    }
    .col-md-4 a {
    text-decoration: none; /* Прибирає підкреслення */
    color: inherit; /* Робить колір тексту стандартним */
}

.col-md-4 a:hover {
    color: inherit; /* Зберігає колір при наведенні */
}

</style>

@section('content')
<div class="container mt-5">
    <div class="search-header text-center mb-4">
        <div class="search-icon">
            <span class="material-icons">search</span>
        </div>
        <h2 class="search-title">Знайдіть ідеальне помешкання</h2>
        <p class="search-subtitle">Обирайте серед найкращих пропозицій у вашому регіоні</p>
    </div>


    {{-- Форма пошуку --}}
    <div class="card p-4 shadow">
        <form id="searchForm">
            <div class="row align-items-end">
                {{-- Місце розташування --}}
                <div class="col-md-3">
                    <label for="region">
                        <span class="material-icons icon">place</span>Місце розташування
                    </label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Введіть місто">
                </div>

                {{-- Дата заїзду --}}
                <div class="col-md-2">
                    <label for="check_in">
                        <span class="material-icons icon">event</span> Дата заїзду
                    </label>
                    <input type="text" class="form-control datepicker" id="check_in" name="check_in">
                </div>

                {{-- Дата виїзду --}}
                <div class="col-md-2">
                    <label for="check_out">
                        <span class="material-icons icon">event</span> Дата виїзду
                    </label>
                    <input type="text" class="form-control datepicker" id="check_out" name="check_out">
                </div>

                {{-- Кількість осіб --}}
                <div class="col-md-2">
                    <label for="guests">
                        <span class="material-icons icon">group</span> Кількість осіб
                    </label>
                    <input type="number" class="form-control" id="guests" name="guests" min="1" value="1">
                </div>

                {{-- Кнопка пошуку --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100 text-white">Шукати</button>
                </div>

            </div>
        </form>
    </div>

    {{-- Контейнер для результатів пошуку --}}
    <div class="mt-4" id="searchResults"></div>
    <p class="text-muted">
    Стільки помешкань знайдено: {{ isset($accommodations) ? $accommodations->count() : 0 }}
</p>

</div>
<div class="container mt-5">
    <div class="row">
        {{-- Ліва колонка для фільтрів --}}
        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h5>Фільтри</h5>

                {{-- Ціна --}}
                <div class="mb-3">
                    <label class="form-label">Ціна (грн)</label>
                    <input type="number" class="form-control mb-2" id="minPrice" placeholder="Від">
                    <input type="number" class="form-control" id="maxPrice" placeholder="До">
                </div>

                {{-- Тип помешкання --}}
                <div class="mb-3">
                    <label class="form-label">Тип</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="hotel" id="typeHotel">
                        <label class="form-check-label" for="typeHotel">Готель</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="apartment" id="typeApartment">
                        <label class="form-check-label" for="typeApartment">Апартаменти</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="house" id="typeHouse">
                        <label class="form-check-label" for="typeHouse">Будинок</label>
                    </div>
                </div>

                {{-- Рейтинг --}}
                <div class="mb-3">
                    <label class="form-label">Рейтинг</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="4" id="rating4">
                        <label class="form-check-label" for="rating4">4+ зірки</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="3" id="rating3">
                        <label class="form-check-label" for="rating3">3+ зірки</label>
                    </div>
                </div>

                {{-- Кнопка застосування фільтрів --}}
                <button class="btn btn-success w-100" id="applyFilters">Застосувати</button>
            </div>
        </div>

        {{-- Права колонка з картками результатів --}}
        <div class="col-md-9">
            <div class="row" id="searchResults">
                @foreach($accommodations as $accommodation)
                <div class="col-md-4 mb-4">
                <a href="{{ route('accommodations.show', $accommodation->id) }}">

                        <div class="card shadow-sm position-relative">
                            {{-- Отримання першого фото з таблиці accommodation_photos --}}
                            @php
                            $mainPhoto = $accommodation->photos->first()->photo_path ?? 'images/default.jpg';
                            @endphp

                            {{-- Зображення --}}
                            <img src="{{ asset($mainPhoto) }}" class="card-img-top" alt="{{ $accommodation->name }}">

                            {{-- Лайк --}}
                            <button class="btn like-button position-absolute top-0 end-0 m-2">
                                <span class="material-icons like-icon text-white">favorite_border</span>
                            </button>

                            <div class="card-body">
                                <h5 class="card-title">{{ $accommodation->name }}</h5>

                                {{-- Локація --}}
                                <p class="text-muted">
                                    <span class="material-icons icon">location_on</span>
                                    {{ $accommodation->settlement }}, {{ $accommodation->region }}
                                </p>

                                {{-- Опис --}}
                                <p class="text-truncate description">{{ $accommodation->description }}</p>

                                {{-- Ціна --}}
                                <p class="price">{{ $accommodation->price_per_night }} грн/ніч</p>

                                {{-- Рейтинг --}}
                                <p class="text-muted d-flex align-items-center mt-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span class="fw-bold mx-1">{{ number_format($accommodation->average_rating, 1) }}</span>
                                    <span class="text-secondary">({{ $accommodation->reviews_count }} відгуків)</span>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>





{{-- Підключаємо Flatpickr --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Підключаємо AJAX --}}
<script>
    document.getElementById("searchForm").addEventListener("submit", function(event) {
        event.preventDefault();

        let formData = new FormData(this);
        let params = new URLSearchParams();

        // Додаємо лише заповнені поля
        formData.forEach((value, key) => {
            if (value.trim() !== "") {
                params.append(key, value);
            }
        });

        let url = "{{ route('accommodations.search') }}" + "?" + params.toString();

        fetch(url, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById("searchResults").innerHTML = html;
            })
            .catch(error => console.error("Помилка пошуку:", error));
    });
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr(".datepicker", {
            enableTime: false, // Без вибору часу
            dateFormat: "Y-m-d", // Формат YYYY-MM-DD
            minDate: "today", // Мінімальна дата - сьогодні
            locale: "uk" // Українська локалізація
        });
    });
</script>

{{-- Підключення Material Icons --}}
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

@endsection
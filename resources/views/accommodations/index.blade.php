@extends('layouts.app')

<style>
    .card {
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border: none;
        background: #fff;
        height: 100%;
    }
    a, a:hover, a:visited, a:active {
        text-decoration: none !important;
        color: inherit !important;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }
    .card-img-top {
        height: 150px;
        object-fit: cover;
        border-radius: 15px 15px 0 0;
    }
    .description {
        font-size: 14px;
        color: #666;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .card-title {
        font-size: 20px;
        font-weight: 600;
        color: #333;
    }
    .card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .price {
        font-size: 18px;
        font-weight: bold;
        color: #28a745;
    }
    .icon {
        vertical-align: middle;
        margin-right: 5px;
        color: #28a745;
    }
    .btn-green {
        background-color: #28a745;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        color: #fff;
        transition: background-color 0.3s;
    }
    .btn-green:hover {
        background-color: #218838;
    }
    .btn-reset {
        background-color: #dc3545;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        color: #fff;
        transition: background-color 0.3s;
    }
    .btn-reset:hover {
        background-color: #c82333;
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
    }
    .search-header {
        text-align: center;
        margin-bottom: 30px;
    }
    .search-icon {
        background: #28a745;
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
    .form-control {
        border-radius: 10px;
        border: 1px solid #E5E7EB;
        padding: 10px;
    }
    .form-control:focus {
        border-color: #28a745;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.3);
    }
    .filter-section {
        max-height: 200px;
        overflow-y: auto;
        margin-bottom: 15px;
    }
    .filter-section::-webkit-scrollbar {
        width: 6px;
    }
    .filter-section::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .filter-section::-webkit-scrollbar-thumb {
        background: #28a745;
        border-radius: 10px;
    }
    .category-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 3px;
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

    <div class="card p-4 shadow">
        <form id="searchForm">
            <div class="row align-items-end">
                <div class="col-md-2">
                    <label for="region">
                        <span class="material-icons icon">place</span> Регіон
                    </label>
                    <select class="form-control" id="region" name="region">
                        <option value="">Оберіть регіон</option>
                        @foreach($regions as $region)
                            <option value="{{ $region }}">{{ $region }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="settlement">
                        <span class="material-icons icon">place</span> Населений пункт
                    </label>
                    <select class="form-control" id="settlement" name="settlement" disabled>
                        <option value="">Спочатку оберіть регіон</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="check_in">
                        <span class="material-icons icon">event</span> Дата заїзду
                    </label>
                    <input type="text" class="form-control datepicker" id="check_in" name="check_in">
                </div>
                <div class="col-md-2">
                    <label for="check_out">
                        <span class="material-icons icon">event</span> Дата виїзду
                    </label>
                    <input type="text" class="form-control datepicker" id="check_out" name="check_out">
                </div>
                <div class="col-md-1">
                    <label for="guests">
                        <span class="material-icons icon">group</span> Особи
                    </label>
                    <input type="number" class="form-control" id="guests" name="guests" min="1" value="">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success w-100 text-white">Шукати</button>
                </div>
            </div>
        </form>
    </div>

    <p class="text-muted mt-4" id="resultsCount">
        Стільки помешкань знайдено: {{ isset($accommodations) ? $accommodations->count() : 0 }}
    </p>
</div>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card p-3 shadow-sm">
                <h5>Фільтри</h5>
                <div class="mb-3">
                    <label class="form-label">Сортування за рейтингом</label>
                    <select class="form-control" id="sortRating" name="sort_rating">
                        <option value="">Без сортування</option>
                        <option value="asc">Від гіршого до кращого</option>
                        <option value="desc">Від кращого до гіршого</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ціна (грн)</label>
                    <input type="number" class="form-control mb-2" id="minPrice" name="minPrice" placeholder="Від" min="0">
                    <input type="number" class="form-control" id="maxPrice" name="maxPrice" placeholder="До" min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Зручності</label>
                    <div class="filter-section">
                        @foreach($amenityCategories as $category)
                            @if($category->amenities->isNotEmpty())
                                <div class="category-label">{{ $category->name }}</div>
                                @foreach($category->amenities as $amenity)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="{{ $amenity->id }}" id="amenity{{ $amenity->id }}" name="amenities[]">
                                        <label class="form-check-label" for="amenity{{ $amenity->id }}">{{ $amenity->name }}</label>
                                    </div>
                                @endforeach
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-success w-50" id="applyFilters">Застосувати</button>
                    <button class="btn btn-reset w-50" id="resetFilters">Скинути</button>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="row" id="initialResults">
                @forelse($accommodations as $accommodation)
                <div class="col-md-4 mb-4">
                    <a href="{{ route('accommodations.show', $accommodation->id) }}">
                        <div class="card shadow-sm position-relative">
                            @php
                            $mainPhoto = $accommodation->photos->first()->photo_path ?? 'images/default.jpg';
                            $isFavorited = auth()->check() && auth()->user()->hasFavorited($accommodation->id);
                            @endphp
                            <img src="{{ asset($mainPhoto) }}" class="card-img-top" alt="{{ $accommodation->name }}">
                            @if(auth()->check())
                            <button class="btn like-button position-absolute top-0 end-0 m-2 toggle-favorite"
                                    data-accommodation-id="{{ $accommodation->id }}"
                                    data-is-favorited="{{ $isFavorited ? 'true' : 'false' }}">
                                <span class="material-icons like-icon {{ $isFavorited ? 'text-danger' : 'text-white' }}">
                                    {{ $isFavorited ? 'favorite' : 'favorite_border' }}
                                </span>
                            </button>
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $accommodation->name }}</h5>
                                <p class="text-muted">
                                    <span class="material-icons icon">location_on</span>
                                    {{ $accommodation->settlement }}, {{ $accommodation->region }}
                                </p>
                                <p class="description">{{ $accommodation->description }}</p>
                                <p class="price">{{ $accommodation->price_per_night }} грн/ніч</p>
                                <p class="text-muted d-flex align-items-center mt-2">
                                    <span class="material-icons text-warning">star</span>
                                    <span class="fw-bold mx-1">{{ number_format($accommodation->average_rating, 1) }}</span>
                                    <span class="text-secondary">({{ $accommodation->reviews_count }} відгуків)</span>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-center text-muted">Помешкань за вибраними критеріями не знайдено.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr(".datepicker", {
            enableTime: false,
            dateFormat: "Y-m-d",
            minDate: "today",
            locale: "uk",
            allowInput: true,
            defaultDate: null
        });

        document.getElementById('region').addEventListener('change', function() {
            const region = this.value;
            const settlementSelect = document.getElementById('settlement');
            settlementSelect.innerHTML = '<option value="">Оберіть населений пункт</option>';
            settlementSelect.disabled = true;

            if (region) {
                fetch(`/settlements-by-region?region=${encodeURIComponent(region)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(settlements => {
                    settlementSelect.disabled = false;
                    if (settlements.length === 0) {
                        settlementSelect.innerHTML = '<option value="">Немає доступних населених пунктів</option>';
                    } else {
                        settlements.forEach(settlement => {
                            const option = document.createElement('option');
                            option.value = settlement;
                            option.textContent = settlement;
                            settlementSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Помилка завантаження сіл:', error);
                    settlementSelect.innerHTML = '<option value="">Помилка завантаження</option>';
                });
            }
        });

        function collectFilterParams() {
            let params = new URLSearchParams();
            const searchForm = document.getElementById('searchForm');
            const formData = new FormData(searchForm);
            formData.forEach((value, key) => {
                if (value.trim() !== "" && value !== "Оберіть регіон" && value !== "Спочатку оберіть регіон") {
                    params.append(key, value);
                }
            });

            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            const sortRating = document.getElementById('sortRating').value;
            if (minPrice) params.append('minPrice', minPrice);
            if (maxPrice) params.append('maxPrice', maxPrice);
            if (sortRating) params.append('sort_rating', sortRating);

            const amenities = Array.from(document.querySelectorAll('input[name="amenities[]"]:checked')).map(input => input.value);
            if (amenities.length > 0) params.append('amenities', amenities.join(','));

            return params;
        }

        function applyFilters() {
            const params = collectFilterParams();
            let url = "{{ route('accommodations.search') }}" + (params.toString() ? "?" + params.toString() : "");

            fetch(url, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("initialResults").innerHTML = new DOMParser()
                    .parseFromString(data.html, 'text/html')
                    .querySelector('#initialResults').innerHTML;
                document.getElementById("resultsCount").textContent = `Стільки помешкань знайдено: ${data.count}`;
                updateFavoriteButtons();
            })
            .catch(error => console.error("Помилка пошуку:", error));
        }

        function resetFilters() {
            document.getElementById('searchForm').reset();
            document.getElementById('settlement').innerHTML = '<option value="">Спочатку оберіть регіон</option>';
            document.getElementById('settlement').disabled = true;
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';
            document.getElementById('sortRating').value = '';
            document.querySelectorAll('input[name="amenities[]"]').forEach(checkbox => checkbox.checked = false);

            fetch("{{ route('accommodations.search') }}", {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("initialResults").innerHTML = new DOMParser()
                    .parseFromString(data.html, 'text/html')
                    .querySelector('#initialResults').innerHTML;
                document.getElementById("resultsCount").textContent = `Стільки помешкань знайдено: ${data.count}`;
                updateFavoriteButtons();
            })
            .catch(error => console.error("Помилка скидання:", error));
        }

        document.getElementById("searchForm").addEventListener("submit", function(event) {
            event.preventDefault();
            applyFilters();
        });

        document.getElementById("applyFilters").addEventListener("click", function(event) {
            event.preventDefault();
            applyFilters();
        });

        document.getElementById("resetFilters").addEventListener("click", function(event) {
            event.preventDefault();
            resetFilters();
        });

        function updateFavoriteButtons() {
            document.querySelectorAll('.toggle-favorite').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const accommodationId = this.getAttribute('data-accommodation-id');
                    let isFavorited = this.getAttribute('data-is-favorited') === 'true';

                    fetch(`/favorites/toggle/${accommodationId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        isFavorited = data.is_favorited;
                        this.setAttribute('data-is-favorited', isFavorited.toString());
                        const icon = this.querySelector('.like-icon');
                        icon.textContent = isFavorited ? 'favorite' : 'favorite_border';
                        icon.classList.toggle('text-danger', isFavorited);
                        icon.classList.toggle('text-white', !isFavorited);
                    })
                    .catch(error => {
                        console.error('Помилка:', error);
                        alert('Будь ласка, увійдіть, щоб додати до улюблених');
                    });
                });
            });
        }

        updateFavoriteButtons();
    });
</script>
<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection
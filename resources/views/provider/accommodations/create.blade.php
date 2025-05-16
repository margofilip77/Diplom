@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --secondary-color: #f3f4f6;
        --text-color: #111827;
        --border-color: #e5e7eb;
        --error-color: #ef4444;
        --success-color: #10b981;
    }

    .dark {
        --primary-color: #6366f1;
        --primary-hover: #4f46e5;
        --secondary-color: #1f2937;
        --text-color: #f9fafb;
        --border-color: #374151;
        --error-color: #f87171;
        --success-color: #34d399;
    }

    .container {
        max-width: 1200px;
        padding: 2rem;
    }

    h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 2rem;
    }

    .form-section {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .dark .form-section {
        background-color: #1f2937;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        background-color: white;
        color: var(--text-color);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .dark .form-control,
    .dark .form-select {
        background-color: #374151;
        border-color: #4b5563;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }

    .form-check {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.25rem;
        appearance: none;
        cursor: pointer;
        position: relative;
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-input:checked::after {
        content: "✓";
        position: absolute;
        color: white;
        font-size: 0.75rem;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .form-check-label {
        color: var(--text-color);
        cursor: pointer;
    }

    .card {
        background-color: white;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .dark .card {
        background-color: #374151;
        border-color: #4b5563;
    }

    .card-body {
        padding: 1.25rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: background-color 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
    }

    .alert {
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background-color: #ecfdf5;
        color: #065f46;
        border-left: 4px solid var(--success-color);
    }

    .dark .alert-success {
        background-color: #064e3b;
        color: #d1fae5;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
        border-left: 4px solid var(--error-color);
    }

    .dark .alert-danger {
        background-color: #7f1d1d;
        color: #fee2e2;
    }

    .invalid-feedback,
    .text-danger {
        color: var(--error-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .is-invalid {
        border-color: var(--error-color);
    }

    #map {
        height: 400px;
        width: 100%;
        border-radius: 0.5rem;
        margin-top: 0.5rem;
        z-index: 1;
    }

    .form-text {
        color: #6b7280;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .dark .form-text {
        color: #9ca3af;
    }

    .amenity-category {
        margin-bottom: 1.5rem;
    }

    .amenity-category-title {
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.75rem;
    }

    .meal-option-card {
        margin-bottom: 1rem;
    }

    .meal-option-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
        .container {
            padding: 1rem;
        }

        .form-section {
            padding: 1.5rem;
        }
    }
</style>

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Додати нове помешкання</h1>

    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('provider.accommodations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Основна інформація -->
        <div class="form-section dark:bg-gray-800">
            <h2 class="form-section-title">Основна інформація</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="name" class="form-label">Назва помешкання</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price_per_night" class="form-label">Ціна за ніч (грн)</label>
                    <input type="number" name="price_per_night" id="price_per_night" step="0.01" min="0" class="form-control @error('price_per_night') is-invalid @enderror" value="{{ old('price_per_night') }}" required>
                    @error('price_per_night')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Короткий опис</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3" required>{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="detailed_description" class="form-label">Детальний опис</label>
                <textarea name="detailed_description" id="detailed_description" class="form-control @error('detailed_description') is-invalid @enderror" rows="5">{{ old('detailed_description') }}</textarea>
                @error('detailed_description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Деталі помешкання -->
        <div class="form-section dark:bg-gray-800">
            <h2 class="form-section-title">Деталі помешкання</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="capacity" class="form-label">Кількість осіб</label>
                    <input type="number" name="capacity" id="capacity" min="1" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity') }}" required>
                    @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="beds" class="form-label">Ліжка</label>
                    <input type="text" name="beds" id="beds" class="form-control @error('beds') is-invalid @enderror" value="{{ old('beds') }}" required placeholder="Наприклад: 2 односпальні, 1 двоспальне">
                    @error('beds')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="children" class="form-label">Діти</label>
                    <select name="children" id="children" class="form-control @error('children') is-invalid @enderror" required>
                        <option value="">Оберіть варіант</option>
                        <option value="allowed" {{ old('children') == 'allowed' ? 'selected' : '' }}>Дозволено</option>
                        <option value="not_allowed" {{ old('children') == 'not_allowed' ? 'selected' : '' }}>Не дозволено</option>
                    </select>
                    @error('children')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="age_restrictions" class="form-label">Мінімальний вік для реєстрації</label>
                    <input type="number" name="age_restrictions" id="age_restrictions" min="0" class="form-control @error('age_restrictions') is-invalid @enderror" value="{{ old('age_restrictions') }}" required>
                    @error('age_restrictions')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="pets_allowed" class="form-label">Дозволено з домашніми улюбленцями</label>
                    <select name="pets_allowed" id="pets_allowed" class="form-control @error('pets_allowed') is-invalid @enderror" required>
                        <option value="">Оберіть варіант</option>
                        <option value="yes" {{ old('pets_allowed') == 'yes' ? 'selected' : '' }}>Так</option>
                        <option value="no" {{ old('pets_allowed') == 'no' ? 'selected' : '' }}>Ні</option>
                    </select>
                    @error('pets_allowed')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="parties_allowed" class="form-label">Дозволено вечірки або заходи</label>
                    <select name="parties_allowed" id="parties_allowed" class="form-control @error('parties_allowed') is-invalid @enderror" required>
                        <option value="">Оберіть варіант</option>
                        <option value="yes" {{ old('parties_allowed') == 'yes' ? 'selected' : '' }}>Так</option>
                        <option value="no" {{ old('parties_allowed') == 'no' ? 'selected' : '' }}>Ні</option>
                    </select>
                    @error('parties_allowed')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="checkin_time" class="form-label">Час заїзду</label>
                    <input type="time" name="checkin_time" id="checkin_time" class="form-control @error('checkin_time') is-invalid @enderror" value="{{ old('checkin_time') }}" required>
                    @error('checkin_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="checkout_time" class="form-label">Час виїзду</label>
                    <input type="time" name="checkout_time" id="checkout_time" class="form-control @error('checkout_time') is-invalid @enderror" value="{{ old('checkout_time') }}" required>
                    @error('checkout_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="is_available" class="form-label">Доступність для бронювання</label>
                    <select name="is_available" id="is_available" class="form-control @error('is_available') is-invalid @enderror" required>
                        <option value="">Оберіть варіант</option>
                        <option value="1" {{ old('is_available') == '1' ? 'selected' : '' }}>Доступно</option>
                        <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>Недоступно</option>
                    </select>
                    @error('is_available')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        <!-- Розташування -->
        <div class="form-section dark:bg-gray-800">
            <h2 class="form-section-title">Розташування</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="region_id" class="form-label">Регіон</label>
                    <select name="region_id" id="region_id" class="form-control @error('region_id') is-invalid @enderror" required>
                        <option value="">Оберіть регіон</option>
                        @foreach($regions as $region)
                        <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                        @endforeach
                        <option value="new" {{ old('region_id') == 'new' ? 'selected' : '' }}>Інший регіон</option>
                    </select>
                    @error('region_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" id="new_region_group" style="display: none;">
                    <label for="new_region" class="form-label">Новий регіон</label>
                    <input type="text" name="new_region" id="new_region" class="form-control @error('new_region') is-invalid @enderror" value="{{ old('new_region') }}">
                    @error('new_region')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="settlement" class="form-label">Населений пункт</label>
                    <input type="text" name="settlement" id="settlement" class="form-control @error('settlement') is-invalid @enderror" value="{{ old('settlement') }}" required>
                    @error('settlement')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Виберіть місце на карті</label>
                <div id="map"></div>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', 48.3794) }}">
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', 31.1656) }}">
            </div>
        </div>


        <!-- Зручності -->
        <div class="form-section dark:bg-gray-800">
            <h2 class="form-section-title">Зручності</h2>

            @foreach($amenity_categories as $category)
            <div class="amenity-category">
                <h3 class="amenity-category-title">{{ $category->category_name }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($category->amenities as $amenity)
                    <div class="form-check">
                        <input type="checkbox" name="amenities[]" id="amenity_{{ $amenity->id }}" value="{{ $amenity->id }}" class="form-check-input" {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}>
                        <label for="amenity_{{ $amenity->id }}" class="form-check-label">{{ $amenity->name }}</label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            @error('amenities')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Типи харчування -->
        <div class="form-section dark:bg-gray-800">
            <h2 class="form-section-title">Типи харчування</h2>

            @foreach($meal_options as $meal_option)
            <div class="meal-option-card card dark:bg-gray-700">
                <div class="card-body">
                    <div class="meal-option-header">
                        <input type="checkbox" name="meal_options[{{ $meal_option->id }}][selected]" id="meal_option_{{ $meal_option->id }}" value="1" class="form-check-input" {{ old("meal_options.{$meal_option->id}.selected") ? 'checked' : '' }}>
                        <label for="meal_option_{{ $meal_option->id }}" class="form-check-label ml-2">{{ $meal_option->name }}</label>
                    </div>
                    <div class="form-group mt-2">
                        <label for="meal_option_price_{{ $meal_option->id }}" class="form-label">Ціна за гостя (грн)</label>
                        <input type="number" name="meal_options[{{ $meal_option->id }}][price]" id="meal_option_price_{{ $meal_option->id }}" step="0.01" min="0" class="form-control @error(" meal_options.{$meal_option->id}.price") is-invalid @enderror" value="{{ old("meal_options.{$meal_option->id}.price") }}" placeholder="Введіть ціну">
                        @error("meal_options.{$meal_option->id}.price")
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            @endforeach
            @error('meal_options')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Фотографії -->
        <div class="form-section dark:bg-gray-800">
            <h2 class="form-section-title">Фотографії</h2>

            <div class="form-group">
                <label for="photos" class="form-label">Завантажити фотографії</label>
                <input type="file" name="photos[]" id="photos" class="form-control @error('photos.*') is-invalid @enderror" multiple>
                <small class="form-text">Можна завантажити кілька фотографій одночасно</small>
                @error('photos.*')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Політика скасування -->
        <div class="form-section dark:bg-gray-800">
            <h2 class="form-section-title">Політика скасування</h2>

            <div class="form-group">
                <textarea name="cancellation_policy" id="cancellation_policy" class="form-control @error('cancellation_policy') is-invalid @enderror" rows="6" readonly>{{ "1. Скасування до 14 днів до заселення – передоплата повертається за вирахуванням банківських комісій.\n2. Скасування менш ніж за 14 днів – передоплата не повертається, оскільки вона покриває витрати виконавця.\n3. Відсутність оплати у встановлений термін – бронювання автоматично анулюється.\n4. Скорочення строку проживання після заселення – виконавець має право отримати компенсацію (1-2 доби оренди) та переглянути загальну вартість послуг відповідно до фактичного строку проживання.\n5. Завершення оренди – відбувається після перевірки стану помешкання виконавцем." }}</textarea>
                @error('cancellation_policy')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-6">
            <button type="submit" class="btn-primary px-6 py-3 text-lg">
                Додати помешкання
            </button>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const regionSelect = document.getElementById('region_id');
        const newRegionGroup = document.getElementById('new_region_group');
        const newRegionInput = document.getElementById('new_region');

        function toggleNewRegionField() {
            const isNew = regionSelect.value === 'new';
            newRegionGroup.style.display = isNew ? 'block' : 'none';
            newRegionInput.required = isNew;
        }

        regionSelect.addEventListener('change', toggleNewRegionField);
        toggleNewRegionField(); // Викликаємо при завантаженні сторінки

        // Ініціалізація карти
        var map = L.map('map').setView([48.3794, 31.1656], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([48.3794, 31.1656], {
            draggable: true
        }).addTo(map);
        marker.on('dragend', function(event) {
            var position = marker.getLatLng();
            document.getElementById('latitude').value = position.lat;
            document.getElementById('longitude').value = position.lng;
        });

        map.on('click', function(event) {
            marker.setLatLng(event.latlng);
            document.getElementById('latitude').value = event.latlng.lat;
            document.getElementById('longitude').value = event.latlng.lng;
        });

        // Заборона негативних значень
        function preventNegativeInput(input, minValue = 0) {
            input.addEventListener('keydown', function(event) {
                if (event.key === '-') {
                    event.preventDefault();
                }
            });
            input.addEventListener('input', function() {
                if (this.value < minValue) {
                    this.value = minValue;
                }
            });
        }

        const pricePerNightInput = document.getElementById('price_per_night');
        preventNegativeInput(pricePerNightInput);

        document.querySelectorAll('input[id^="meal_option_price_"]').forEach(input => {
            preventNegativeInput(input);
        });

        const capacityInput = document.getElementById('capacity');
        preventNegativeInput(capacityInput, 1);

        const ageRestrictionsInput = document.getElementById('age_restrictions');
        preventNegativeInput(ageRestrictionsInput);
    });
</script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


@endsection
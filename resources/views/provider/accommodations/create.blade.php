@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Додати нове помешкання</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('provider.accommodations.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-4">
            <label for="name" class="form-label">Назва помешкання</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="description" class="form-label">Опис</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="price_per_night" class="form-label">Ціна за ніч (грн)</label>
            <input type="number" name="price_per_night" id="price_per_night" step="0.01" min="0" class="form-control @error('price_per_night') is-invalid @enderror" value="{{ old('price_per_night') }}" required>
            @error('price_per_night')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="capacity" class="form-label">Кількість осіб</label>
            <input type="number" name="capacity" id="capacity" min="1" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity') }}" required>
            @error('capacity')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="detailed_description" class="form-label">Детальна інформація</label>
            <textarea name="detailed_description" id="detailed_description" class="form-control @error('detailed_description') is-invalid @enderror" rows="4">{{ old('detailed_description') }}</textarea>
            @error('detailed_description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="children" class="form-label">Діти</label>
            <select name="children" id="children" class="form-control @error('children') is-invalid @enderror" required>
                <option value="">Оберіть варіант</option>
                <option value="allowed" {{ old('children') == 'allowed' ? 'selected' : '' }}>Дозволено</option>
                <option value="not_allowed" {{ old('children') == 'not_allowed' ? 'selected' : '' }}>Не дозволено</option>
                <option value="has_cribs" {{ old('children') == 'has_cribs' ? 'selected' : '' }}>Є дитячі ліжечка</option>
            </select>
            @error('children')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="beds" class="form-label">Ліжка (наприклад, "2 односпальних, 1 двоспальне")</label>
            <input type="text" name="beds" id="beds" class="form-control @error('beds') is-invalid @enderror" value="{{ old('beds') }}" required>
            @error('beds')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="age_restrictions" class="form-label">Мінімальний вік для реєстрації</label>
            <input type="number" name="age_restrictions" id="age_restrictions" min="0" class="form-control @error('age_restrictions') is-invalid @enderror" value="{{ old('age_restrictions') }}" required>
            @error('age_restrictions')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
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

        <div class="form-group mb-4">
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

        <div class="form-group mb-4">
            <label for="checkin_time" class="form-label">Час заїзду</label>
            <input type="time" name="checkin_time" id="checkin_time" class="form-control @error('checkin_time') is-invalid @enderror" value="{{ old('checkin_time') }}" required>
            @error('checkin_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="checkout_time" class="form-label">Час виїзду</label>
            <input type="time" name="checkout_time" id="checkout_time" class="form-control @error('checkout_time') is-invalid @enderror" value="{{ old('checkout_time') }}" required>
            @error('checkout_time')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="region_id" class="form-label">Регіон</label>
            <select name="region_id" id="region_id" class="form-control @error('region_id') is-invalid @enderror" required>
                <option value="">Оберіть регіон</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                @endforeach
                <option value="new">Інший регіон</option>
            </select>
            @error('region_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4" id="new_region_group" style="display: none;">
            <label for="new_region" class="form-label">Новий регіон</label>
            <input type="text" name="new_region" id="new_region" class="form-control" value="{{ old('new_region') }}">
        </div>

        <div class="form-group mb-4">
            <label for="settlement" class="form-label">Населений пункт</label>
            <input type="text" name="settlement" id="settlement" class="form-control @error('settlement') is-invalid @enderror" value="{{ old('settlement') }}" required>
            @error('settlement')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Оберіть місце на карті</label>
            <div id="map" style="height: 400px; width: 100%; border-radius: 8px;"></div>
            <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}" required>
            <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}" required>
            @error('latitude')
                <div class="text-danger">{{ $message }}</div>
            @enderror
            @error('longitude')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label class="form-label">Зручності</label>
            @foreach($amenity_categories as $category)
                <h5 class="mt-3 mb-2">{{ $category->category_name }}</h5>
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        @foreach($category->amenities as $amenity)
                            <div class="form-check mb-2">
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

        <div class="form-group mb-4">
            <label class="form-label">Типи харчування</label>
            @foreach($meal_options as $meal_option)
                <div class="card mb-3 shadow-sm">
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input type="checkbox" name="meal_options[{{ $meal_option->id }}][selected]" id="meal_option_{{ $meal_option->id }}" value="1" class="form-check-input" {{ old("meal_options.{$meal_option->id}.selected") ? 'checked' : '' }}>
                            <label for="meal_option_{{ $meal_option->id }}" class="form-check-label">{{ $meal_option->name }}</label>
                        </div>
                        <div class="form-group mb-2">
                            <label for="meal_option_price_{{ $meal_option->id }}">Ціна за гостя (грн)</label>
                            <input type="number" name="meal_options[{{ $meal_option->id }}][price]" id="meal_option_price_{{ $meal_option->id }}" step="0.01" min="0" class="form-control @error("meal_options.{$meal_option->id}.price") is-invalid @enderror" value="{{ old("meal_options.{$meal_option->id}.price") }}" placeholder="Введіть ціну">
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

        <div class="form-group mb-4">
            <label for="photos" class="form-label">Фотографії (опціонально)</label>
            <input type="file" name="photos[]" id="photos" class="form-control-file @error('photos.*') is-invalid @enderror" multiple>
            <small class="form-text text-muted">Можна завантажити кілька фотографій одночасно. Утримуйте Ctrl (або Cmd на Mac), щоб обрати кілька файлів.</small>
            @error('photos.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="cancellation_policy" class="form-label">Політика скасування</label>
            <textarea name="cancellation_policy" id="cancellation_policy" class="form-control @error('cancellation_policy') is-invalid @enderror" rows="6" readonly>{{ "1. Скасування до 14 днів до заселення – передоплата повертається за вирахуванням банківських комісій.\n2. Скасування менш ніж за 14 днів – передоплата не повертається, оскільки вона покриває витрати виконавця.\n3. Відсутність оплати у встановлений термін – бронювання автоматично анулюється.\n4. Скорочення строку проживання після заселення – виконавець має право отримати компенсацію (1-2 доби оренди) та переглянути загальну вартість послуг відповідно до фактичного строку проживання.\n5. Завершення оренди – відбувається після перевірки стану помешкання виконавцем." }}</textarea>
            @error('cancellation_policy')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="is_available" class="form-label">Доступність</label>
            <select name="is_available" id="is_available" class="form-control @error('is_available') is-invalid @enderror" required>
                <option value="">Оберіть варіант</option>
                <option value="1" {{ old('is_available') == '1' ? 'selected' : '' }}>Доступно</option>
                <option value="0" {{ old('is_available') == '0' ? 'selected' : '' }}>Недоступно</option>
            </select>
            @error('is_available')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Додати помешкання</button>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
    .form-check-input:checked {
        background-color: #007bff;
        border-color: #007bff;
    }
    .form-check-label {
        cursor: pointer;
    }
    .card {
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
    .form-control, .form-select {
        border-radius: 6px;
        box-shadow: none;
    }
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .btn-primary {
        border-radius: 6px;
        padding: 10px 20px;
    }
</style>

<script>
document.getElementById('region_id').addEventListener('change', function() {
    let newRegionGroup = document.getElementById('new_region_group');
    if (this.value === 'new') {
        newRegionGroup.style.display = 'block';
        document.getElementById('new_region').required = true;
        document.getElementById('region_id').name = 'region_id_temp';
        document.getElementById('new_region').name = 'region_id';
    } else {
        newRegionGroup.style.display = 'none';
        document.getElementById('new_region').required = false;
        document.getElementById('region_id').name = 'region_id';
        document.getElementById('new_region').name = 'new_region';
    }
});

var map = L.map('map').setView([48.3794, 31.1656], 6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var marker = L.marker([48.3794, 31.1656], { draggable: true }).addTo(map);
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

// Забороняємо введення знака "-" у числові поля
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

// Застосовуємо до поля price_per_night
const pricePerNightInput = document.getElementById('price_per_night');
preventNegativeInput(pricePerNightInput);

// Застосовуємо до всіх полів meal_option_price
document.querySelectorAll('input[id^="meal_option_price_"]').forEach(input => {
    preventNegativeInput(input);
});

// Застосовуємо до поля capacity (мін. значення 1)
const capacityInput = document.getElementById('capacity');
preventNegativeInput(capacityInput, 1);

// Застосовуємо до поля age_restrictions (мін. значення 0)
const ageRestrictionsInput = document.getElementById('age_restrictions');
preventNegativeInput(ageRestrictionsInput);
</script>
@endsection
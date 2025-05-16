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

    .form-control, .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.375rem;
        background-color: white;
        color: var(--text-color);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .dark .form-control, .dark .form-select {
        background-color: #374151;
        border-color: #4b5563;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
    }

    textarea.form-control {
        min-height: 120px;
        resize: vertical;
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

    .invalid-feedback, .text-danger {
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
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Додати нову послугу</h1>

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

    <form action="{{ route('provider.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Основна інформація -->
        <div class="form-section dark:bg-gray-800">
            <h2 class="form-section-title">Основна інформація</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="form-group">
                    <label for="name" class="form-label">Назва послуги</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="price" class="form-label">Ціна (грн)</label>
                    <input type="number" name="price" id="price" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                    @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="duration" class="form-label">Тривалість (хвилини)</label>
                    <input type="number" name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration') }}" required>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Категорія</label>
                    <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                        <option value="">Оберіть категорію</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="availability" class="form-label">Доступність для бронювання</label>
                    <select name="availability" id="availability" class="form-control @error('availability') is-invalid @enderror" required>
                        <option value="1" {{ old('availability') == 1 ? 'selected' : '' }}>Так</option>
                        <option value="0" {{ old('availability') == 0 ? 'selected' : '' }}>Ні</option>
                    </select>
                    <small class="form-text">Вкажіть, чи доступна послуга для бронювання</small>
                    @error('availability')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Опис послуги</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
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
                        <option value="new">Інший регіон</option>
                    </select>
                    @error('region_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" id="new_region_group" style="display: none;">
                    <label for="new_region" class="form-label">Новий регіон</label>
                    <input type="text" name="new_region" id="new_region" class="form-control" value="{{ old('new_region') }}">
                </div>

                <div class="form-group">
                    <label for="settlement" class="form-label">Населений пункт</label>
                    <input type="text" name="settlement" id="settlement" class="form-control @error('settlement') is-invalid @enderror" value="{{ old('settlement') }}" required>
                    @error('settlement')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-group mt-4">
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

        <div class="mt-6">
            <button type="submit" class="btn-primary px-6 py-3 text-lg">
                Додати послугу
            </button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обробка вибору регіону
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

    // Ініціалізація карти
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

    // Заборона введення негативних значень
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

    // Застосування до числових полів
    const priceInput = document.getElementById('price');
    preventNegativeInput(priceInput);

    const durationInput = document.getElementById('duration');
    preventNegativeInput(durationInput, 1);
});
</script>
@endsection
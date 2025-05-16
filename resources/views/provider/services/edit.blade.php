@extends('layouts.app')

@section('title', 'Редагувати послугу')

@section('content')
<div class="container mt-4">
    <h1>Редагувати послугу</h1>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
            <a href="{{ route('provider.dashboard') }}" class="btn btn-primary mt-2">Повернутися до панелі</a>
        </div>
    @else
        <form action="{{ route('provider.services.update', $service) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Назва послуги</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $service->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Опис</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="price">Ціна (грн)</label>
                <input type="number" step="0.01" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $service->price) }}" required>
                @error('price')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="region_id">Регіон</label>
                <select name="region_id" id="region_id" class="form-control @error('region_id') is-invalid @enderror" required>
                    <option value="">Оберіть регіон</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" {{ old('region_id', $service->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                    @endforeach
                    <option value="new" {{ old('region_id') == 'new' ? 'selected' : '' }}>Інший регіон</option>
                </select>
                @error('region_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" id="new_region_group" style="display: none;">
                <label for="new_region">Новий регіон</label>
                <input type="text" name="new_region" id="new_region" class="form-control @error('new_region') is-invalid @enderror" value="{{ old('new_region') }}">
                @error('new_region')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="settlement">Населений пункт</label>
                <input type="text" name="settlement" id="settlement" class="form-control @error('settlement') is-invalid @enderror" value="{{ old('settlement', $service->settlement) }}" required>
                @error('settlement')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="category_id">Категорія</label>
                <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                    <option value="">Оберіть категорію</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $service->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="map">Оберіть локацію на карті</label>
                <div id="map" style="height: 400px; width: 100%;"></div>
                <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $service->latitude) }}" required>
                <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $service->longitude) }}" required>
                <p>Координати: <span id="coordinates">Широта: {{ old('latitude', $service->latitude) }}, Довгота: {{ old('longitude', $service->longitude) }}</span></p>
            </div>

            <div class="form-group">
                <label for="image">Зображення (необов’язково)</label>
                @if ($service->image)
                    <div>
                        <img src="{{ asset('storage/' . $service->image) }}" alt="Зображення послуги" style="max-width: 200px; height: auto;">
                    </div>
                @endif
                <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_available">Доступність</label>
                <select name="is_available" id="is_available" class="form-control @error('is_available') is-invalid @enderror" required>
                    <option value="1" {{ old('is_available', $service->is_available) ? 'selected' : '' }}>Доступно</option>
                    <option value="0" {{ old('is_available', $service->is_available) ? '' : 'selected' }}>Недоступно</option>
                </select>
                @error('is_available')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Оновити послугу</button>
        </form>
    @endif
</div>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Обробка поля для нового регіону
    const regionSelect = document.getElementById('region_id');
    const newRegionGroup = document.getElementById('new_region_group');
    const newRegionInput = document.getElementById('new_region');

    function toggleNewRegionField() {
        const isNew = regionSelect.value === 'new';
        newRegionGroup.style.display = isNew ? 'block' : 'none';
        newRegionInput.required = isNew;
    }

    regionSelect.addEventListener('change', toggleNewRegionField);
    toggleNewRegionField();

    // Ініціалізація карти
    const lat = parseFloat(document.getElementById('latitude').value);
    const lng = parseFloat(document.getElementById('longitude').value);
    var map = L.map('map').setView([lat, lng], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    marker.on('dragend', function(event) {
        var position = marker.getLatLng();
        document.getElementById('latitude').value = position.lat;
        document.getElementById('longitude').value = position.lng;
        document.getElementById('coordinates').innerText = `Широта: ${position.lat}, Довгота: ${position.lng}`;
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitude').value = e.latlng.lat;
        document.getElementById('longitude').value = e.latlng.lng;
        document.getElementById('coordinates').innerText = `Широта: ${e.latlng.lat}, Довгота: ${e.latlng.lng}`;
    });

    // Заборона негативних значень для ціни
    const priceInput = document.getElementById('price');
    priceInput.addEventListener('keydown', function(event) {
        if (event.key === '-') {
            event.preventDefault();
        }
    });
    priceInput.addEventListener('input', function() {
        if (this.value < 0) {
            this.value = 0;
        }
    });

    // Обмеження координат
    const latitudeInput = document.getElementById('latitude');
    latitudeInput.addEventListener('input', function() {
        if (this.value < -90) this.value = -90;
        if (this.value > 90) this.value = 90;
    });

    const longitudeInput = document.getElementById('longitude');
    longitudeInput.addEventListener('input', function() {
        if (this.value < -180) this.value = -180;
        if (this.value > 180) this.value = 180;
    });
});
</script>
@endsection
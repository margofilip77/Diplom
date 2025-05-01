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
                </select>
                @error('region_id')
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
     // Ініціалізація карти
     var map = L.map('map').setView([48.3794, 31.1656], 6); // Центр України

// Додавання тайлів OpenStreetMap
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Початковий маркер
var marker = L.marker([48.3794, 31.1656]).addTo(map);

// Оновлення координат при кліку на карті
map.on('click', function(e) {
    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    // Оновлення маркера
    marker.setLatLng([lat, lng]);

    // Оновлення полів форми
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;

    // Оновлення тексту координат
    document.getElementById('coordinates').innerText = `Широта: ${lat}, Довгота: ${lng}`;
});
</script>
@endsection
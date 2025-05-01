@extends('layouts.app')

@section('title', 'Створити новий пакет')

@section('content')
<div class="container mt-4">
    <h1>Створити новий пакет</h1>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.packages.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Назва пакета</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Опис</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="region_id" class="form-label">Регіон</label>
            <select name="region_id" id="region_id" class="form-select @error('region_id') is-invalid @enderror" required>
                <option value="">Оберіть регіон</option>
                @foreach ($regions as $region)
                    <option value="{{ $region->id }}" {{ old('region_id') == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                @endforeach
            </select>
            @error('region_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="discount" class="form-label">Знижка (5-10%)</label>
            <input type="number" name="discount" id="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount', 5) }}" min="5" max="10" required>
            @error('discount')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="category_filter" class="form-label">Фільтрувати за категорією</label>
            <select id="category_filter" class="form-select">
                <option value="">Всі категорії</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Оберіть послуги</label>
            @if ($categories->pluck('services')->flatten()->isEmpty())
                <p class="text-danger">Немає доступних послуг для вибору.</p>
            @else
                <div id="services_list">
                    @foreach ($categories as $category)
                        @foreach ($category->services as $service)
                        <div class="form-check service-item" data-category-id="{{ $category->id }}" data-region-id="{{ $service->region_id }}" style="display: flex; align-items: center; margin-bottom: 10px;">
    <input type="checkbox" name="services[]" id="service_{{ $service->id }}" value="{{ $service->id }}" class="form-check-input" {{ in_array($service->id, old('services', [])) ? 'checked' : '' }}>
    <label for="service_{{ $service->id }}" class="form-check-label" style="display: flex; align-items: center; width: 100%;">
        <img src="{{ asset('services/' . $service->image) }}" alt="{{ $service->name }}" style="width: 50px; height: 50px; margin-right: 10px; object-fit: cover;">
        <div>
            {{ $service->name }} ({{ $service->price }} грн)
            @if ($service->region)
                <br><small>Регіон: {{ $service->region->name }}</small>
            @endif
        </div>
    </label>
</div>
                        @endforeach
                    @endforeach
                </div>
            @endif
            @error('services')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Створити пакет</button>
        <a href="{{ route('admin.packages') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Фільтрація за регіоном
    document.getElementById('region_id').addEventListener('change', function () {
        const selectedRegionId = this.value;
        const serviceItems = document.querySelectorAll('.service-item');

        serviceItems.forEach(item => {
            const regionId = item.getAttribute('data-region-id');
            if (selectedRegionId === '' || regionId === selectedRegionId) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });

        // Скидаємо фільтр категорій
        document.getElementById('category_filter').value = '';
    });

    // Фільтрація за категорією
    document.getElementById('category_filter').addEventListener('change', function () {
        const selectedCategoryId = this.value;
        const selectedRegionId = document.getElementById('region_id').value;
        const serviceItems = document.querySelectorAll('.service-item');

        serviceItems.forEach(item => {
            const categoryId = item.getAttribute('data-category-id');
            const regionId = item.getAttribute('data-region-id');
            const matchesCategory = selectedCategoryId === '' || categoryId === selectedCategoryId;
            const matchesRegion = selectedRegionId === '' || regionId === selectedRegionId;

            if (matchesCategory && matchesRegion) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection
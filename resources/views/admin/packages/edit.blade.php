@extends('layouts.app')

@section('title', 'Редагувати пакет')

@section('content')
<div class="container mt-4">
    <h1>Редагувати пакет: {{ $package->name }}</h1>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.packages.update', $package->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Назва пакета</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $package->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Опис</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $package->description) }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="discount" class="form-label">Знижка (%)</label>
            <input type="number" name="discount" id="discount" class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount', $package->discount) }}" min="0" max="100" required>
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
                            <div class="form-check service-item" data-category-id="{{ $category->id }}" style="display: flex; align-items: center; margin-bottom: 10px;">
                                <input type="checkbox" name="services[]" id="service_{{ $service->id }}" value="{{ $service->id }}" class="form-check-input" {{ $package->services->contains($service->id) || in_array($service->id, old('services', [])) ? 'checked' : '' }}>
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

        <button type="submit" class="btn btn-primary">Оновити пакет</button>
        <a href="{{ route('admin.packages') }}" class="btn btn-secondary">Назад</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('category_filter').addEventListener('change', function () {
        const selectedCategoryId = this.value;
        const serviceItems = document.querySelectorAll('.service-item');

        serviceItems.forEach(item => {
            const categoryId = item.getAttribute('data-category-id');
            if (selectedCategoryId === '' || categoryId === selectedCategoryId) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>
@endsection
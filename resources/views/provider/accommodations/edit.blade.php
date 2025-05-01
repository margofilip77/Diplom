@extends('layouts.app')

@section('title', 'Редагувати помешкання')

@section('content')
<div class="container mt-4">
    <h1>Редагувати помешкання</h1>

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
            <a href="{{ route('provider.dashboard') }}" class="btn btn-primary mt-2">Повернутися до панелі</a>
        </div>
    @else
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('provider.accommodations.update', $accommodation) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Назва</label>
                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $accommodation->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">Опис</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="5" required>{{ old('description', $accommodation->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="price_per_night">Ціна за ніч (грн)</label>
                <input type="number" step="0.01" name="price_per_night" id="price_per_night" class="form-control @error('price_per_night') is-invalid @enderror" value="{{ old('price_per_night', $accommodation->price_per_night) }}" required>
                @error('price_per_night')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="capacity">Кількість осіб</label>
                <input type="number" name="capacity" id="capacity" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $accommodation->capacity) }}" required>
                @error('capacity')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="detailed_description">Детальний опис</label>
                <textarea name="detailed_description" id="detailed_description" class="form-control @error('detailed_description') is-invalid @enderror" rows="5" required>{{ old('detailed_description', $accommodation->detailed_description) }}</textarea>
                @error('detailed_description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="children">Діти</label>
                <select name="children" id="children" class="form-control @error('children') is-invalid @enderror" required>
                    <option value="allowed" {{ old('children', $accommodation->children) == 'allowed' ? 'selected' : '' }}>Дозволено</option>
                    <option value="not_allowed" {{ old('children', $accommodation->children) == 'not_allowed' ? 'selected' : '' }}>Не дозволено</option>
                    <option value="has_cribs" {{ old('children', $accommodation->children) == 'has_cribs' ? 'selected' : '' }}>Є дитячі ліжечка</option>
                </select>
                @error('children')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="beds">Кількість ліжок</label>
                <input type="number" name="beds" id="beds" class="form-control @error('beds') is-invalid @enderror" value="{{ old('beds', $accommodation->beds) }}" required>
                @error('beds')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="age_restrictions">Мінімальний вік (років)</label>
                <input type="number" name="age_restrictions" id="age_restrictions" class="form-control @error('age_restrictions') is-invalid @enderror" value="{{ old('age_restrictions', $accommodation->age_restrictions) }}" required>
                @error('age_restrictions')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="pets_allowed">Дозволені тварини</label>
                <select name="pets_allowed" id="pets_allowed" class="form-control @error('pets_allowed') is-invalid @enderror" required>
                    <option value="yes" {{ old('pets_allowed', $accommodation->pets_allowed) == 'yes' ? 'selected' : '' }}>Так</option>
                    <option value="no" {{ old('pets_allowed', $accommodation->pets_allowed) == 'no' ? 'selected' : '' }}>Ні</option>
                </select>
                @error('pets_allowed')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="parties_allowed">Дозволені вечірки</label>
                <select name="parties_allowed" id="parties_allowed" class="form-control @error('parties_allowed') is-invalid @enderror" required>
                    <option value="yes" {{ old('parties_allowed', $accommodation->parties_allowed) == 'yes' ? 'selected' : '' }}>Так</option>
                    <option value="no" {{ old('parties_allowed', $accommodation->parties_allowed) == 'no' ? 'selected' : '' }}>Ні</option>
                </select>
                @error('parties_allowed')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="checkin_time">Час заїзду</label>
                <input type="time" name="checkin_time" id="checkin_time" class="form-control @error('checkin_time') is-invalid @enderror" value="{{ old('checkin_time', $accommodation->checkin_time) }}" required>
                @error('checkin_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="checkout_time">Час виїзду</label>
                <input type="time" name="checkout_time" id="checkout_time" class="form-control @error('checkout_time') is-invalid @enderror" value="{{ old('checkout_time', $accommodation->checkout_time) }}" required>
                @error('checkout_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="region_id">Регіон</label>
                <select name="region_id" id="region_id" class="form-control @error('region_id') is-invalid @enderror" required>
                    <option value="">Оберіть регіон</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" {{ old('region_id', $accommodation->region_id) == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                    @endforeach
                </select>
                @error('region_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="settlement">Населений пункт</label>
                <input type="text" name="settlement" id="settlement" class="form-control @error('settlement') is-invalid @enderror" value="{{ old('settlement', $accommodation->settlement) }}" required>
                @error('settlement')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="latitude">Широта</label>
                <input type="number" step="any" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude', $accommodation->latitude) }}" required>
                @error('latitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="longitude">Довгота</label>
                <input type="number" step="any" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude', $accommodation->longitude) }}" required>
                @error('longitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="photos">Фотографії (необов’язково)</label>
                @if($accommodation->photos->isNotEmpty())
                    <div class="mb-3">
                        <p>Поточні фотографії:</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                            @foreach($accommodation->photos as $photo)
                                <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Фото" style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.375rem;">
                            @endforeach
                        </div>
                        <p>Завантажте нові фотографії, щоб замінити поточні.</p>
                    </div>
                @endif
                <input type="file" name="photos[]" id="photos" class="form-control-file @error('photos.*') is-invalid @enderror" multiple>
                @error('photos.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_available">Доступність</label>
                <select name="is_available" id="is_available" class="form-control @error('is_available') is-invalid @enderror" required>
                    <option value="1" {{ old('is_available', $accommodation->is_available) ? 'selected' : '' }}>Доступно</option>
                    <option value="0" {{ old('is_available', $accommodation->is_available) ? '' : 'selected' }}>Недоступно</option>
                </select>
                @error('is_available')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Оновити помешкання</button>
        </form>
    @endif
</div>
@endsection
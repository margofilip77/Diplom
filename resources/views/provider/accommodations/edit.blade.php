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
                    <option value="new">Інший регіон</option>
                </select>
                @error('region_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group" id="new_region_group" style="display: none;">
                <label for="new_region">Новий регіон</label>
                <input type="text" name="new_region" id="new_region" class="form-control" value="{{ old('new_region') }}">
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
                <label for="is_available">Доступність для бронювання</label>
                <select name="is_available" id="is_available" class="form-control @error('is_available') is-invalid @enderror" required>
                    <option value="1" {{ old('is_available', $accommodation->is_available) ? 'selected' : '' }}>Доступно</option>
                    <option value="0" {{ old('is_available', $accommodation->is_available) ? '' : 'selected' }}>Недоступно</option>
                </select>
                @error('is_available')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Розділ для редагування дат бронювань -->
            <div class="form-group mt-5">
                <h3>Бронювання для цього помешкання</h3>
                @if($accommodation->bookings->isEmpty())
                    <p>Наразі немає бронювань для цього помешкання.</p>
                @else
                    <div class="row">
                        @foreach($accommodation->bookings as $booking)
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm" style="border-radius: 15px;">
                                    <div class="card-body">
                                        <h5 class="card-title fw-bold">Бронювання #{{ $booking->id }}</h5>
                                        <p class="mb-1">
                                            <strong>Дата заїзду:</strong> {{ $booking->checkin_date }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Дата виїзду:</strong> {{ $booking->checkout_date }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Сума:</strong> {{ $booking->total_price }} грн
                                        </p>
                                        <p class="mb-1">
                                            <strong>Ім'я:</strong> {{ $booking->name }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Email:</strong> {{ $booking->email }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Телефон:</strong> {{ $booking->phone }}
                                        </p>
                                        @if($booking->comments)
                                            <p class="mb-1">
                                                <strong>Коментарі:</strong> {{ $booking->comments }}
                                            </p>
                                        @endif

                                        <!-- Логіка для кнопки зміни дат -->
                                        @php
                                            $checkinDate = \Carbon\Carbon::parse($booking->checkin_date);
                                            $currentDate = \Carbon\Carbon::now();
                                            $daysUntilCheckin = $currentDate->diffInDays($checkinDate, false);
                                            $canEdit = $daysUntilCheckin > 14;
                                        @endphp

                                        <div class="mt-3">
                                            <button class="btn btn-primary" style="border-radius: 25px;" data-bs-toggle="modal" data-bs-target="#editBookingDatesModal{{ $booking->id }}" @if(!$canEdit) disabled @endif>
                                                Змінити дати
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Модальне вікно для зміни дат бронювання -->
                            <div class="modal fade" id="editBookingDatesModal{{ $booking->id }}" tabindex="-1" aria-labelledby="editBookingDatesModalLabel{{ $booking->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editBookingDatesModalLabel{{ $booking->id }}">Змінити дати бронювання #{{ $booking->id }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ route('provider.accommodation.booking.update.dates', [$accommodation->id, $booking->id]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="mb-3">
                                                    <label for="checkin_date_{{ $booking->id }}" class="form-label">Дата заїзду</label>
                                                    <input type="date" class="form-control" id="checkin_date_{{ $booking->id }}" name="checkin_date" value="{{ $booking->checkin_date }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="checkout_date_{{ $booking->id }}" class="form-label">Дата виїзду</label>
                                                    <input type="date" class="form-control" id="checkout_date_{{ $booking->id }}" name="checkout_date" value="{{ $booking->checkout_date }}" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary" style="border-radius: 25px;">Зберегти зміни</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">Оновити помешкання</button>
        </form>
    @endif
</div>

<style>
    .form-group {
        margin-bottom: 1rem;
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
    .btn:disabled {
        background: #cccccc;
        cursor: not-allowed;
    }
    .card:hover {
        transform: translateY(-5px);
        transition: all 0.3s ease;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15) !important;
    }
</style>

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

    const capacityInput = document.getElementById('capacity');
    preventNegativeInput(capacityInput, 1);

    const bedsInput = document.getElementById('beds');
    preventNegativeInput(bedsInput, 1);

    const ageRestrictionsInput = document.getElementById('age_restrictions');
    preventNegativeInput(ageRestrictionsInput);

    const latitudeInput = document.getElementById('latitude');
    preventNegativeInput(latitudeInput, -90);
    latitudeInput.addEventListener('input', function() {
        if (this.value > 90) this.value = 90;
    });

    const longitudeInput = document.getElementById('longitude');
    preventNegativeInput(longitudeInput, -180);
    longitudeInput.addEventListener('input', function() {
        if (this.value > 180) this.value = 180;
    });
});
</script>
@endsection
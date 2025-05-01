@extends('layouts.app')

@section('title', 'Очікувані пропозиції - Адмін-панель')

@section('content')
<div class="container mt-4">
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
        <a href="{{ route('home') }}" class="btn btn-primary mt-2">Повернутися на головну</a>
    </div>
    @else
    <h1>Очікувані пропозиції</h1>
    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif

    <h2 class="mt-5">Послуги</h2>
    @if ($services->isEmpty())
    <p>Немає очікуваних послуг.</p>
    @else
    <div class="table-responsive">
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Опис</th>
                    <th>Ціна</th>
                    <th>Регіон</th>
                    <th>Населений пункт</th>
                    <th>Категорія</th>
                    <th>Тривалість</th>
                    <th>Зображення</th>
                    <th>Надавач</th>
                    <th>Доступність</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($services as $service)
                <tr>
                    <td>{{ $service->id }}</td>
                    <td>{{ $service->name }}</td>
                    <td>{{ $service->description }}</td>
                    <td>{{ $service->price }} грн</td>
                    <td>{{ $service->region ? $service->region->name : 'Немає' }}</td>
                    <td>{{ $service->settlement }}</td>
                    <td>{{ $service->category ? $service->category->name : 'Немає' }}</td>
                    <td>{{ $service->duration ?? 'Не вказано' }}</td>
                    <td>
                        @if ($service->image)
                            <img src="{{ asset('storage/' . $service->image) }}" alt="Зображення послуги" style="max-width: 100px; height: auto;">
                        @else
                            Не завантажено
                        @endif
                    </td>
                    <td>{{ $service->user ? $service->user->name : 'Немає' }}</td>
                    <td>{{ $service->is_available ? 'Доступно' : 'Недоступно' }}</td>
                    <td>{{ $service->status }}</td>
                    <td>
                        <form action="{{ route('admin.services.approve', $service) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Підтвердити</button>
                        </form>
                        <!-- Кнопка для відкриття модального вікна -->
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectServiceModal{{ $service->id }}">
                            Відхилити
                        </button>

                        <!-- Модальне вікно для введення причини відхилення -->
                        <div class="modal fade" id="rejectServiceModal{{ $service->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectServiceModalLabel{{ $service->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectServiceModalLabel{{ $service->id }}">Відхилення послуги</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.services.reject', $service) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="rejection_reason">Причина відхилення</label>
                                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Скасувати</button>
                                            <button type="submit" class="btn btn-danger">Відхилити</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <h2 class="mt-5">Помешкання</h2>
    @if ($accommodations->isEmpty())
    <p>Немає очікуваних помешкань.</p>
    @else
    <div class="table-responsive">
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Назва</th>
                    <th>Опис</th>
                    <th>Ціна за ніч</th>
                    <th>Кількість осіб</th>
                    <th>Детальний опис</th>
                    <th>Діти</th>
                    <th>Ліжка</th>
                    <th>Мін. вік</th>
                    <th>Тварини</th>
                    <th>Вечірки</th>
                    <th>Час заїзду</th>
                    <th>Час виїзду</th>
                    <th>Регіон</th>
                    <th>Населений пункт</th>
                    <th>Фотографії</th>
                    <th>Надавач</th>
                    <th>Доступність</th>
                    <th>Причина відхилення</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accommodations as $accommodation)
                <tr>
                    <td>{{ $accommodation->id }}</td>
                    <td>{{ $accommodation->name }}</td>
                    <td>{{ $accommodation->description }}</td>
                    <td>{{ $accommodation->price_per_night }} грн</td>
                    <td>{{ $accommodation->capacity }}</td>
                    <td>{{ $accommodation->detailed_description ?? 'Не вказано' }}</td>
                    <td>
                        @if($accommodation->children == 'allowed')
                            Дозволено
                        @elseif($accommodation->children == 'not_allowed')
                            Не дозволено
                        @elseif($accommodation->children == 'has_cribs')
                            Є дитячі ліжечка
                        @else
                            Не вказано
                        @endif
                    </td>
                    <td>{{ $accommodation->beds }}</td>
                    <td>{{ $accommodation->age_restrictions }}</td>
                    <td>{{ $accommodation->pets_allowed == 'yes' ? 'Так' : 'Ні' }}</td>
                    <td>{{ $accommodation->parties_allowed == 'yes' ? 'Так' : 'Ні' }}</td>
                    <td>{{ $accommodation->checkin_time }}</td>
                    <td>{{ $accommodation->checkout_time }}</td>
                    <td>{{ $accommodation->region }}</td>
                    <td>{{ $accommodation->settlement }}</td>
                    <td>
                        @if($accommodation->photos->isEmpty())
                            <span>Немає фотографій</span>
                        @else
                            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                @foreach($accommodation->photos as $photo)
                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Фото" style="width: 100px; height: auto; object-fit: cover; border-radius: 0.375rem;">
                                @endforeach
                            </div>
                        @endif
                    </td>
                    <td>{{ $accommodation->user ? $accommodation->user->name : 'Немає' }}</td>
                    <td>{{ $accommodation->is_available ? 'Доступно' : 'Недоступно' }}</td>
                    <td>{{ $accommodation->rejection_reason ?? '-' }}</td>
                    <td>
                        <form action="{{ route('admin.accommodations.approve', $accommodation) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">Підтвердити</button>
                        </form>
                        <!-- Кнопка для відкриття модального вікна -->
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectAccommodationModal{{ $accommodation->id }}">
                            Відхилити
                        </button>

                        <!-- Модальне вікно для введення причини відхилення -->
                        <div class="modal fade" id="rejectAccommodationModal{{ $accommodation->id }}" tabindex="-1" role="dialog" aria-labelledby="rejectAccommodationModalLabel{{ $accommodation->id }}" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectAccommodationModalLabel{{ $accommodation->id }}">Відхилення помешкання</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('admin.accommodations.reject', $accommodation) }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="rejection_reason">Причина відхилення</label>
                                                <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Скасувати</button>
                                            <button type="submit" class="btn btn-danger">Відхилити</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
    @endif
</div>

<!-- Додаємо залежності для модального вікна (Bootstrap JS та jQuery) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
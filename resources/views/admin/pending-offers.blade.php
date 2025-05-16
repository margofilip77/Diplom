@extends('layouts.app')

@section('title', 'Очікувані пропозиції - Адмін-панель')

@section('content')
<div class="container mt-4">
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <a href="{{ route('home') }}" class="btn btn-primary btn-sm ms-2">Повернутися на головну</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @else
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Очікувані пропозиції</h1>
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        <ul class="nav nav-tabs mb-4" id="proposalTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="true">Послуги</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="accommodations-tab" data-bs-toggle="tab" data-bs-target="#accommodations" type="button" role="tab" aria-controls="accommodations" aria-selected="false">Помешкання</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Таб для послуг -->
            <div class="tab-pane fade show active" id="services" role="tabpanel" aria-labelledby="services-tab">
                @if ($services->isEmpty())
                    <div class="alert alert-info text-center" role="alert">
                        Немає очікуваних послуг.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
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
                                    <td>{{ Str::limit($service->description, 50, '...') }}</td>
                                    <td>{{ $service->price }} грн</td>
                                    <td>{{ $service->region ? $service->region->name : 'Немає' }}</td>
                                    <td>{{ $service->settlement }}</td>
                                    <td>{{ $service->category ? $service->category->name : 'Немає' }}</td>
                                    <td>{{ $service->duration ?? 'Не вказано' }}</td>
                                    <td>
                                        @if ($service->image)
                                            <img src="{{ asset('storage/' . $service->image) }}" alt="Зображення послуги" class="img-fluid rounded" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                        @else
                                            <span class="text-muted">Не завантажено</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($service->user)
                                            <div>{{ $service->user->name }}</div>
                                            <small class="text-muted">{{ $service->user->email }}</small>
                                        @else
                                            <span class="text-muted">Немає</span>
                                        @endif
                                    </td>
                                    <td>{{ $service->is_available ? 'Доступно' : 'Недоступно' }}</td>
                                    <td>{{ $service->status }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.services.approve', $service) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Підтвердити</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectServiceModal{{ $service->id }}">
                                                Відхилити
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Модальне вікно для відхилення -->
                                <div class="modal fade" id="rejectServiceModal{{ $service->id }}" tabindex="-1" aria-labelledby="rejectServiceModalLabel{{ $service->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectServiceModalLabel{{ $service->id }}">Відхилення послуги</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.services.reject', $service) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="rejection_reason" class="form-label">Причина відхилення</label>
                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                                                    <button type="submit" class="btn btn-danger">Відхилити</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Таб для помешкань -->
            <div class="tab-pane fade" id="accommodations" role="tabpanel" aria-labelledby="accommodations-tab">
                @if ($accommodations->isEmpty())
                    <div class="alert alert-info text-center" role="alert">
                        Немає очікуваних помешкань.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle">
                            <thead class="table-light">
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
                                    <td>{{ Str::limit($accommodation->description, 50, '...') }}</td>
                                    <td>{{ $accommodation->price_per_night }} грн</td>
                                    <td>{{ $accommodation->capacity }}</td>
                                    <td>{{ Str::limit($accommodation->detailed_description ?? 'Не вказано', 50, '...') }}</td>
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
                                            <span class="text-muted">Немає фотографій</span>
                                        @else
                                            <div class="d-flex gap-2">
                                                @foreach($accommodation->photos->take(3) as $photo)
                                                    <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Фото" class="img-fluid rounded" style="max-width: 80px; max-height: 80px; object-fit: cover;">
                                                @endforeach
                                                @if($accommodation->photos->count() > 3)
                                                    <span class="text-muted">(+{{ $accommodation->photos->count() - 3 }})</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($accommodation->user)
                                            <div>{{ $accommodation->user->name }}</div>
                                            <small class="text-muted">{{ $accommodation->user->email }}</small>
                                        @else
                                            <span class="text-muted">Немає</span>
                                        @endif
                                    </td>
                                    <td>{{ $accommodation->is_available ? 'Доступно' : 'Недоступно' }}</td>
                                    <td>{{ $accommodation->rejection_reason ?? '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.accommodations.approve', $accommodation) }}" method="POST" style="display:inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success">Підтвердити</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectAccommodationModal{{ $accommodation->id }}">
                                                Відхилити
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Модальне вікно для відхилення -->
                                <div class="modal fade" id="rejectAccommodationModal{{ $accommodation->id }}" tabindex="-1" aria-labelledby="rejectAccommodationModalLabel{{ $accommodation->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="rejectAccommodationModalLabel{{ $accommodation->id }}">Відхилення помешкання</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('admin.accommodations.reject', $accommodation) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="rejection_reason" class="form-label">Причина відхилення</label>
                                                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                                                    <button type="submit" class="btn btn-danger">Відхилити</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Підключення Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection
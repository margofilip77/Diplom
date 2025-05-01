@extends('layouts.app')

@section('content')
<style>
    /* Стилі для кнопок */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        text-align: center;
        border: 1px solid transparent;
        padding: 0.25rem 0.75rem; /* Зменшено відступи */
        font-size: 0.875rem; /* Зменшено розмір шрифту (14px) */
        line-height: 1.25rem; /* Вирівнювання тексту */
        border-radius: 0.25rem; /* Менший радіус для сучасного вигляду */
        transition: all 0.2s ease; /* Плавний перехід */
        text-decoration: none;
        white-space: nowrap; /* Запобігає перенесенню тексту */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); /* М’яка тінь */
    }

    .btn:hover {
        transform: scale(1.05); /* Легке збільшення при наведенні */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Тінь при наведенні */
    }

    .btn-primary {
        background-color: #2563eb; /* Синій колір (blue-600) */
        border-color: #2563eb;
        color: #ffffff;
    }

    .btn-primary:hover {
        background-color: #1d4ed8; /* Темніший синій (blue-700) */
        border-color: #1d4ed8;
    }

    .btn-warning {
        background-color: #f59e0b; /* Жовтий колір (yellow-500) */
        border-color: #f59e0b;
        color: #ffffff;
    }

    .btn-warning:hover {
        background-color: #d97706; /* Темніший жовтий (yellow-600) */
        border-color: #d97706;
    }

    .btn-success {
        background-color: #22c55e; /* Зелений колір (green-500) */
        border-color: #22c55e;
        color: #ffffff;
    }

    .btn-success:hover {
        background-color: #16a34a; /* Темніший зелений (green-600) */
        border-color: #16a34a;
    }

    .btn-danger {
        background-color: #ef4444; /* Червоний колір (red-500) */
        border-color: #ef4444;
        color: #ffffff;
    }

    .btn-danger:hover {
        background-color: #dc2626; /* Темніший червоний (red-600) */
        border-color: #dc2626;
    }

    /* Контейнер для кнопок у колонці "Дії" */
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem; /* Відстань між кнопками */
        align-items: center;
    }

    /* Стилі для таблиць */
    .table {
        width: 100%;
        border-collapse: collapse;
        background-color: #ffffff;
    }

    .table th,
    .table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
    }

    .table thead {
        background-color: #f3f4f6;
    }

    .table th {
        font-weight: 600;
        color: #111827;
    }

    .table td {
        color: #374151;
    }

    /* Темна тема */
    .dark .table {
        background-color: #1f2937;
    }

    .dark .table thead {
        background-color: #374151;
    }

    .dark .table th {
        color: #ffffff;
    }

    .dark .table td {
        color: #d1d5db;
    }

    .dark .table td, 
    .dark .table th {
        border-bottom: 1px solid #4b5563;
    }

    /* Стилі для повідомлень */
    .alert-success {
        background-color: #dcfce7;
        border-left: 4px solid #22c55e;
        color: #166534;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 0.375rem;
    }

    .alert-danger {
        background-color: #fee2e2;
        border-left: 4px solid #ef4444;
        color: #991b1b;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 0.375rem;
    }
</style>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Панель надавача</h1>

    <!-- Вибір між додаванням помешкання та послуги -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <a href="{{ route('provider.services.create') }}" class="btn btn-primary">Додати послугу</a>
        <a href="{{ route('provider.accommodations.create') }}" class="btn btn-primary">Додати помешкання</a>
    </div>

    <!-- Повідомлення про успішне створення або помилки -->
    @if (session('error'))
        <div class="alert-danger text-medium">
            {{ session('error') }}
        </div>
    @endif

    @if (session('status') == 'service-submitted')
        <div class="alert-success text-medium">
            Послугу надіслано на затвердження адміністратору!
        </div>
    @elseif (session('status') == 'accommodation-submitted')
        <div class="alert-success text-medium">
            Помешкання надіслано на затвердження адміністратору!
        </div>
    @elseif (session('status') == 'service-deleted')
        <div class="alert-success text-medium">
            Послугу успішно видалено!
        </div>
    @elseif (session('status') == 'accommodation-deleted')
        <div class="alert-success text-medium">
            Помешкання успішно видалено!
        </div>
    @elseif (session('status') == 'Послугу надіслано на перевірку.')
        <div class="alert-success text-medium">
            Послугу надіслано на перевірку!
        </div>
    @elseif (session('status') == 'Помешкання надіслано на перевірку.')
        <div class="alert-success text-medium">
            Помешкання надіслано на перевірку!
        </div>
    @endif

    <!-- Сповіщення про підтвердження -->
    @php
        $approvedServices = $services->where('status', 'approved');
        $approvedAccommodations = $accommodations->where('status', 'approved');
    @endphp

    @if ($approvedServices->isNotEmpty())
        @foreach ($approvedServices as $service)
            <div class="alert-success text-medium">
                Ваша послуга "{{ $service->name }}" успішно опублікована!
            </div>
        @endforeach
    @endif

    @if ($approvedAccommodations->isNotEmpty())
        @foreach ($approvedAccommodations as $accommodation)
            <div class="alert-success text-medium">
                Ваше помешкання "{{ $accommodation->name }}" успішно опубліковане!
            </div>
        @endforeach
    @endif

    <!-- Список інтерфейсу -->
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Ваші послуги</h2>
    @if($services->isEmpty())
        <p class="text-gray-600 dark:text-gray-300 mb-6 text-light">У вас ще немає створених послуг.</p>
    @else
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Назва</th>
                        <th>Ціна</th>
                        <th>Категорія</th>
                        <th>Регіон</th>
                        <th>Населений пункт</th>
                        <th>Статус</th>
                        <th>Доступність</th>
                        <th>Причина відхилення</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                        <tr>
                            <td class="text-medium">{{ $service->name }}</td>
                            <td>{{ $service->price }} грн</td>
                            <td>
                                {{ $service->category ? $service->category->name : 'Немає категорії' }}
                            </td>
                            <td>
                                {{ $service->region ? $service->region->name : $service->region_id }}
                            </td>
                            <td>{{ $service->settlement }}</td>
                            <td>
                                @if($service->status == 'pending')
                                    <span class="text-yellow-600 dark:text-yellow-400 text-medium">Очікує підтвердження</span>
                                @elseif($service->status == 'approved')
                                    <span class="text-green-600 dark:text-green-400 text-medium">Підтверджено</span>
                                @else
                                    <span class="text-red-600 dark:text-red-400 text-medium">Відхилено</span>
                                @endif
                            </td>
                            <td>{{ $service->is_available ? 'Доступно' : 'Недоступно' }}</td>
                            <td>
                                @if($service->status == 'rejected' && $service->rejection_reason)
                                    <span class="text-red-600 dark:text-red-400">{{ $service->rejection_reason }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('provider.services.edit', $service) }}" class="btn btn-warning">Редагувати</a>
                                    @if($service->status == 'rejected')
                                        <form action="{{ route('provider.services.resubmit', $service) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="alert('Надсилаємо послугу на перевірку...');">Надіслати на перевірку</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('provider.services.destroy', $service) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити цю послугу?');">Видалити</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Список існуючих помешкань -->
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mt-8 mb-4">Ваші помешкання</h2>
    @if($accommodations->isEmpty())
        <p class="text-gray-600 dark:text-gray-300 mb-6 text-light">У вас ще немає створених помешкань.</p>
    @else
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Назва</th>
                        <th>Локація</th>
                        <th>Ціна за ніч</th>
                        <th>Кількість осіб</th>
                        <th>Регіон</th>
                        <th>Населений пункт</th>
                        <th>Детальний опис</th>
                        <th>Діти</th>
                        <th>Ліжка</th>
                        <th>Мін. вік</th>
                        <th>Тварини</th>
                        <th>Вечірки</th>
                        <th>Час заїзду</th>
                        <th>Час виїзду</th>
                        <th>Фотографії</th>
                        <th>Статус</th>
                        <th>Доступність</th>
                        <th>Причина відхилення</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accommodations as $accommodation)
                        <tr>
                            <td class="text-medium">{{ $accommodation->name }}</td>
                            <td>{{ $accommodation->location }}</td>
                            <td>{{ $accommodation->price_per_night }} грн</td>
                            <td>{{ $accommodation->capacity }}</td>
                            <td>{{ $accommodation->region }}</td>
                            <td>{{ $accommodation->settlement }}</td>
                            <td>{{ Str::limit($accommodation->detailed_description, 100) }}</td>
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
                            <td>
                                @if($accommodation->photos->isEmpty())
                                    <span class="text-gray-600 dark:text-gray-400">Немає фотографій</span>
                                @else
                                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                                        @foreach($accommodation->photos as $photo)
                                            <img src="{{ asset('storage/' . $photo->photo_path) }}" alt="Фото" style="width: 3rem; height: 3rem; object-fit: cover; border-radius: 0.375rem;">
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($accommodation->status == 'pending')
                                    <span class="text-yellow-600 dark:text-yellow-400 text-medium">Очікує підтвердження</span>
                                @elseif($accommodation->status == 'approved')
                                    <span class="text-green-600 dark:text-green-400 text-medium">Підтверджено</span>
                                @else
                                    <span class="text-red-600 dark:text-red-400 text-medium">Відхилено</span>
                                @endif
                            </td>
                            <td>{{ $accommodation->is_available ? 'Доступно' : 'Недоступно' }}</td>
                            <td>
                                @if($accommodation->status == 'rejected' && $accommodation->rejection_reason)
                                    <span class="text-red-600 dark:text-red-400">{{ $accommodation->rejection_reason }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('provider.accommodations.edit', $accommodation) }}" class="btn btn-warning">Редагувати</a>
                                    @if($accommodation->status == 'rejected')
                                        <form action="{{ route('provider.accommodations.resubmit', $accommodation) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="alert('Надсилаємо помешкання на перевірку...');">Надіслати на перевірку</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('provider.accommodations.destroy', $accommodation) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити це помешкання?');">Видалити</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
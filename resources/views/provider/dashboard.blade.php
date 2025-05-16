@extends('layouts.app')

@section('content')
<style>
    /* Додаткові стилі для модального вікна опису */
    .description-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        overflow: auto;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .description-modal.show {
        opacity: 1;
    }

    .description-modal-content {
        position: relative;
        max-width: 90%;
        max-height: 90vh;
        margin: 5vh auto;
        background-color: #fff;
        padding: 2rem;
        border-radius: 0.5rem;
        color: #111827;
        overflow-y: auto;
    }

    .dark .description-modal-content {
        background-color: #1f2937;
        color: #f9fafb;
    }

    .close-description {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        background: rgba(0, 0, 0, 0.5);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .close-description:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: rotate(90deg);
    }
    /* Основні стилі */
    .container {
        max-width: 100%;
        padding: 0 1rem;
    }

    /* Стилі для кнопок */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 500;
        text-align: center;
        border: 1px solid transparent;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        cursor: pointer;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .btn-warning {
        background-color: #f59e0b;
        border-color: #f59e0b;
        color: #ffffff;
    }

    .btn-warning:hover {
        background-color: #d97706;
        border-color: #d97706;
    }

    .btn-success {
        background-color: #10b981;
        border-color: #10b981;
        color: #ffffff;
    }

    .btn-success:hover {
        background-color: #059669;
        border-color: #059669;
    }

    .btn-danger {
        background-color: #ef4444;
        border-color: #ef4444;
        color: #ffffff;
    }

    .btn-danger:hover {
        background-color: #dc2626;
        border-color: #dc2626;
    }

    /* Контейнер для кнопок */
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    /* Стилі для таблиць */
    .table-container {
        overflow-x: auto;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        background-color: #ffffff;
        font-size: 0.875rem;
    }

    .table th,
    .table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e5e7eb;
        vertical-align: top;
    }

    .table thead {
        background-color: #f9fafb;
    }

    .table th {
        font-weight: 600;
        color: #111827;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }

    .table tr:hover {
        background-color: #f9fafb;
    }

    /* Стилі для слайдера фотографій */
    .photo-slider {
        position: relative;
        width: 120px;
        height: 120px;
    }

    .photo-slider-container {
        width: 100%;
        height: 100%;
        overflow: hidden;
        position: relative;
        border-radius: 0.375rem;
    }

    .photo-slides {
        display: flex;
        transition: transform 0.3s ease;
        height: 100%;
    }

    .photo-slide {
        min-width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s ease;
    }

    .photo-slide img:hover {
        transform: scale(1.02);
    }

    .slider-nav {
        position: absolute;
        top: 50%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        transform: translateY(-50%);
        padding: 0 0.5rem;
    }

    .slider-btn {
        background-color: rgba(255, 255, 255, 0.7);
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .slider-btn:hover {
        background-color: rgba(255, 255, 255, 0.9);
    }

    .slider-dots {
        position: absolute;
        bottom: 8px;
        left: 0;
        right: 0;
        display: flex;
        justify-content: center;
        gap: 4px;
    }

    .dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.5);
        transition: all 0.2s ease;
    }

    .dot.active {
        background-color: rgba(255, 255, 255, 0.9);
    }

    /* Модальне вікно для перегляду фото */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        overflow: auto;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .modal.show {
        opacity: 1;
    }

    .modal-content {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        width: 100%;
        position: relative;
    }

    .modal-img {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        animation: zoomIn 0.3s ease;
        cursor: zoom-out;
    }

    @keyframes zoomIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .close {
        position: fixed;
        top: 30px;
        right: 30px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
        background: rgba(0,0,0,0.5);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .close:hover {
        background: rgba(255,255,255,0.2);
        transform: rotate(90deg);
    }

    .modal-nav {
        position: fixed;
        top: 50%;
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0 20px;
        transform: translateY(-50%);
        z-index: 1001;
    }

    .modal-nav-btn {
        background: rgba(0,0,0,0.5);
        color: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 24px;
        transition: all 0.3s ease;
    }

    .modal-nav-btn:hover {
        background: rgba(255,255,255,0.2);
    }

    .modal-counter {
        position: fixed;
        bottom: 30px;
        left: 0;
        right: 0;
        text-align: center;
        color: white;
        font-size: 16px;
        z-index: 1001;
        background: rgba(0,0,0,0.5);
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-block;
        margin: 0 auto;
    }

    /* Стилі для повідомлень */
    .alert {
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 0.375rem;
        border-left: 4px solid;
    }

    .alert-success {
        background-color: #ecfdf5;
        border-left-color: #10b981;
        color: #065f46;
    }

    .alert-danger {
        background-color: #fee2e2;
        border-left-color: #ef4444;
        color: #991b1b;
    }

    /* Заголовки */
    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1.5rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
        margin: 2rem 0 1rem;
    }

    /* Пусті стани */
    .empty-state {
        color: #6b7280;
        padding: 1.5rem;
        text-align: center;
        border: 1px dashed #e5e7eb;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
    }

    /* Статуси */
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }

    .status-approved {
        background-color: #d1fae5;
        color: #065f46;
    }

    .status-rejected {
        background-color: #fee2e2;
        color: #991b1b;
    }

    /* Адаптація для темного режиму */
    .dark .table {
        background-color: #1f2937;
    }

    .dark .table thead {
        background-color: #111827;
    }

    .dark .table th {
        color: #f9fafb;
    }

    .dark .table td {
        color: #e5e7eb;
    }

    .dark .table tr:hover {
        background-color: #1f2937;
    }

    .dark .empty-state {
        border-color: #374151;
        color: #9ca3af;
    }

    .dark .page-title,
    .dark .section-title {
        color: #f9fafb;
    }

    .dark .alert-success {
        background-color: #064e3b;
        border-left-color: #059669;
        color: #d1fae5;
    }

    .dark .alert-danger {
        background-color: #7f1d1d;
        border-left-color: #ef4444;
        color: #fee2e2;
    }
    .orders-tab {
        position: relative;
    }
    .orders-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #ef4444;
        color: white;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>

<div class="container mx-auto px-4 py-8">
    <h1 class="page-title dark:text-white">Панель надавача</h1>

    <!-- Навігація -->
    <div class="flex flex-col sm:flex-row gap-3 mb-6">
        <a href="{{ route('provider.services.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Додати послугу
        </a>
        <a href="{{ route('provider.accommodations.create') }}" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Додати помешкання
        </a>
        <a href="{{ route('provider.orders') }}" class="btn btn-primary orders-tab">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            Замовлення
            @if($activeOrdersCount > 0)
                <span class="orders-badge">{{ $activeOrdersCount }}</span>
            @endif
        </a>
    </div>

    <!-- Повідомлення -->
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('status') == 'service-submitted')
        <div class="alert alert-success">
            Послугу надіслано на затвердження адміністратору!
        </div>
    @elseif (session('status') == 'accommodation-submitted')
        <div class="alert alert-success">
            Помешкання надіслано на затвердження адміністратору!
        </div>
    @elseif (session('status') == 'service-deleted')
        <div class="alert alert-success">
            Послугу успішно видалено!
        </div>
    @elseif (session('status') == 'accommodation-deleted')
        <div class="alert alert-success">
            Помешкання успішно видалено!
        </div>
    @elseif (session('status') == 'Послугу надіслано на перевірку.')
        <div class="alert alert-success">
            Послугу надіслано на перевірку!
        </div>
    @elseif (session('status') == 'Помешкання надіслано на перевірку.')
        <div class="alert alert-success">
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
            <div class="alert alert-success">
                Ваша послуга "{{ $service->name }}" успішно опублікована!
            </div>
        @endforeach
    @endif

    @if ($approvedAccommodations->isNotEmpty())
        @foreach ($approvedAccommodations as $accommodation)
            <div class="alert alert-success">
                Ваше помешкання "{{ $accommodation->name }}" успішно опубліковане!
            </div>
        @endforeach
    @endif

    <!-- Список послуг -->
    <h2 class="section-title dark:text-white">Ваші послуги</h2>
    @if($services->isEmpty())
        <div class="empty-state dark:border-gray-700">
            У вас ще немає створених послуг
        </div>
    @else
        <div class="table-container">
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
                        <th>Фотографії</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($services as $service)
                        <tr>
                            <td class="font-medium">{{ $service->name }}</td>
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
                                    <span class="status-badge status-pending">Очікує підтвердження</span>
                                @elseif($service->status == 'approved')
                                    <span class="status-badge status-approved">Підтверджено</span>
                                @else
                                    <span class="status-badge status-rejected">Відхилено</span>
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
    @if(!$service->image)
        <span class="text-gray-400">Немає фото</span>
    @else
        <div class="service-photo-slider" style="width: 120px; height: 120px;">
            <img src="{{ asset('storage/' . $service->image) }}" 
                 alt="Фото послуги" 
                 class="photo-thumbnail"
                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.375rem; cursor: pointer;"
                 data-src="{{ asset('storage/' . $service->image) }}"
                 data-slider="service-slider-{{ $service->id }}"
                 data-index="0">
        </div>
    @endif
</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('provider.services.edit', $service) }}" class="btn btn-warning">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Редагувати
                                    </a>
                                    @if($service->status == 'rejected')
                                        <form action="{{ route('provider.services.resubmit', $service) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="alert('Надсилаємо послугу на перевірку...');">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                На перевірку
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('provider.services.destroy', $service) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити цю послугу?');">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Видалити
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Список помешкань -->
    <h2 class="section-title dark:text-white">Ваші помешкання</h2>
    @if($accommodations->isEmpty())
        <div class="empty-state dark:border-gray-700">
            У вас ще немає створених помешкань
        </div>
    @else
        <div class="table-container">
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
                        <th>Доступність для бронювання</th>
                        <th>Причина відхилення</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accommodations as $accommodation)
                        <tr>
                            <td class="font-medium">{{ $accommodation->name }}</td>
                            <td>{{ $accommodation->location }}</td>
                            <td>{{ $accommodation->price_per_night }} грн</td>
                            <td>{{ $accommodation->capacity }}</td>
                            <td>{{ $accommodation->region }}</td>
                            <td>{{ $accommodation->settlement }}</td>
                            <td>
                                <button class="btn btn-primary view-description" data-description="{{ $accommodation->detailed_description }}" data-name="{{ $accommodation->name }}">
                                    Переглянути
                                </button>
                            </td>
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
                                    <span class="text-gray-400">Немає фото</span>
                                @else
                                    <div class="photo-slider">
                                        <div class="photo-slider-container">
                                            <div class="photo-slides" id="slider-{{ $accommodation->id }}">
                                                @foreach($accommodation->photos as $photo)
                                                    <div class="photo-slide">
                                                        <img src="{{ asset('storage/' . $photo->photo_path) }}" 
                                                             alt="Фото помешкання" 
                                                             class="photo-thumbnail"
                                                             data-src="{{ asset('storage/' . $photo->photo_path) }}"
                                                             data-slider="slider-{{ $accommodation->id }}"
                                                             data-index="{{ $loop->index }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="slider-nav">
                                            <button class="slider-btn prev-btn" data-slider="slider-{{ $accommodation->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                                </svg>
                                            </button>
                                            <button class="slider-btn next-btn" data-slider="slider-{{ $accommodation->id }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="slider-dots" id="dots-{{ $accommodation->id }}">
                                            @foreach($accommodation->photos as $index => $photo)
                                                <div class="dot {{ $index === 0 ? 'active' : '' }}" data-slider="slider-{{ $accommodation->id }}" data-index="{{ $index }}"></div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($accommodation->status == 'pending')
                                    <span class="status-badge status-pending">Очікує підтвердження</span>
                                @elseif($accommodation->status == 'approved')
                                    <span class="status-badge status-approved">Підтверджено</span>
                                @else
                                    <span class="status-badge status-rejected">Відхилено</span>
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
                                    <a href="{{ route('provider.accommodations.edit', $accommodation) }}" class="btn btn-warning">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Редагувати
                                    </a>
                                    @if($accommodation->status == 'rejected')
                                        <form action="{{ route('provider.accommodations.resubmit', $accommodation) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success" onclick="alert('Надсилаємо помешкання на перевірку...');">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                На перевірку
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('provider.accommodations.destroy', $accommodation) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити це помешкання?');">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Видалити
                                        </button>
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

<!-- Модальне вікно для перегляду фото -->
<div id="photoModal" class="modal">
    <span class="close">×</span>
    
    <div class="modal-nav">
        <button class="modal-nav-btn prev-btn"><</button>
        <button class="modal-nav-btn next-btn">></button>
    </div>
    
    <div class="modal-content">
        <img id="modalImage" class="modal-img" src="" alt="Фото">
    </div>
    
    <div class="modal-counter">
        <span id="currentPhoto">1</span> / <span id="totalPhotos">1</span>
    </div>
</div>

<!-- Модальне вікно для перегляду детального опису -->
<div id="descriptionModal" class="description-modal">
    <span class="close-description">×</span>
    <div class="description-modal-content">
        <h2 id="modalDescriptionTitle" class="text-xl font-bold mb-4"></h2>
        <p id="modalDescriptionText"></p>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ініціалізація слайдерів для помешкань
        const sliders = document.querySelectorAll('.photo-slides');
        sliders.forEach(slider => {
            const sliderId = slider.id;
            const slides = slider.querySelectorAll('.photo-slide');
            const dotsContainer = document.getElementById(`dots-${sliderId.split('-')[1]}`);
            const dots = dotsContainer.querySelectorAll('.dot');
            let currentSlide = 0;

            function updateSlider() {
                slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            }

            document.querySelectorAll(`.prev-btn[data-slider="${sliderId}"]`).forEach(btn => {
                btn.addEventListener('click', () => {
                    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                    updateSlider();
                });
            });

            document.querySelectorAll(`.next-btn[data-slider="${sliderId}"]`).forEach(btn => {
                btn.addEventListener('click', () => {
                    currentSlide = (currentSlide + 1) % slides.length;
                    updateSlider();
                });
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    currentSlide = index;
                    updateSlider();
                });
            });
        });

        // Ініціалізація слайдерів для послуг
        const serviceSliders = document.querySelectorAll('.service-photo-slides');
        serviceSliders.forEach(slider => {
            const sliderId = slider.id;
            const slides = slider.querySelectorAll('.service-photo-slide');
            const dotsContainer = document.getElementById(`service-dots-${sliderId.split('-')[2]}`);
            const dots = dotsContainer.querySelectorAll('.service-dot');
            let currentSlide = 0;

            function updateServiceSlider() {
                slider.style.transform = `translateX(-${currentSlide * 100}%)`;
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentSlide);
                });
            }

            document.querySelectorAll(`.prev-btn[data-slider="${sliderId}"]`).forEach(btn => {
                btn.addEventListener('click', () => {
                    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                    updateServiceSlider();
                });
            });

            document.querySelectorAll(`.next-btn[data-slider="${sliderId}"]`).forEach(btn => {
                btn.addEventListener('click', () => {
                    currentSlide = (currentSlide + 1) % slides.length;
                    updateServiceSlider();
                });
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    currentSlide = index;
                    updateServiceSlider();
                });
            });
        });

        // Модальне вікно для перегляду фото
        const photoModal = document.getElementById('photoModal');
        const modalImg = document.getElementById('modalImage');
        const closePhotoBtn = document.querySelector('.close');
        const prevPhotoBtn = document.querySelector('.modal-nav .prev-btn');
        const nextPhotoBtn = document.querySelector('.modal-nav .next-btn');
        const currentPhotoSpan = document.getElementById('currentPhoto');
        const totalPhotosSpan = document.getElementById('totalPhotos');

        let currentPhotos = [];
        let currentPhotoIndex = 0;
        let currentSliderId = '';

        document.querySelectorAll('.photo-thumbnail').forEach(img => {
            img.addEventListener('click', function() {
                currentSliderId = this.dataset.slider;
                currentPhotos = Array.from(document.getElementById(currentSliderId).querySelectorAll('.photo-slide img')).map(img => img.src);
                currentPhotoIndex = parseInt(this.dataset.index);

                document.body.style.overflow = 'hidden';
                photoModal.style.display = 'block';
                setTimeout(() => photoModal.classList.add('show'), 10);
                updateModalPhoto();
            });
        });

        function updateModalPhoto() {
            modalImg.src = currentPhotos[currentPhotoIndex];
            currentPhotoSpan.textContent = currentPhotoIndex + 1;
            totalPhotosSpan.textContent = currentPhotos.length;

            if (currentSliderId) {
                const slider = document.getElementById(currentSliderId);
                slider.style.transform = `translateX(-${currentPhotoIndex * 100}%)`;
                const dotsContainer = document.getElementById(currentSliderId.includes('service') ? `service-dots-${currentSliderId.split('-')[2]}` : `dots-${currentSliderId.split('-')[1]}`);
                if (dotsContainer) {
                    const dots = dotsContainer.querySelectorAll(currentSliderId.includes('service') ? '.service-dot' : '.dot');
                    dots.forEach((dot, index) => {
                        dot.classList.toggle('active', index === currentPhotoIndex);
                    });
                }
            }
        }

        prevPhotoBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            currentPhotoIndex = (currentPhotoIndex - 1 + currentPhotos.length) % currentPhotos.length;
            updateModalPhoto();
        });

        nextPhotoBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            currentPhotoIndex = (currentPhotoIndex + 1) % currentPhotos.length;
            updateModalPhoto();
        });

        function closePhotoModal() {
            photoModal.classList.remove('show');
            setTimeout(() => {
                photoModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        closePhotoBtn.addEventListener('click', closePhotoModal);
        modalImg.addEventListener('click', (e) => {
            e.stopPropagation();
            closePhotoModal();
        });
        photoModal.addEventListener('click', (e) => {
            if (e.target === photoModal) closePhotoModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && photoModal.style.display === 'block') closePhotoModal();
            if (photoModal.style.display === 'block') {
                if (e.key === 'ArrowLeft') {
                    currentPhotoIndex = (currentPhotoIndex - 1 + currentPhotos.length) % currentPhotos.length;
                    updateModalPhoto();
                } else if (e.key === 'ArrowRight') {
                    currentPhotoIndex = (currentPhotoIndex + 1) % currentPhotos.length;
                    updateModalPhoto();
                }
            }
        });

        let touchStartX = 0;
        let touchEndX = 0;
        modalImg.addEventListener('touchstart', (e) => touchStartX = e.changedTouches[0].screenX, false);
        modalImg.addEventListener('touchend', (e) => {
            touchEndX = e.changedTouches[0].screenX;
            if (touchEndX < touchStartX - 50) {
                currentPhotoIndex = (currentPhotoIndex + 1) % currentPhotos.length;
                updateModalPhoto();
            } else if (touchEndX > touchStartX + 50) {
                currentPhotoIndex = (currentPhotoIndex - 1 + currentPhotos.length) % currentPhotos.length;
                updateModalPhoto();
            }
        }, false);

        // Модальне вікно для перегляду детального опису
        const descriptionModal = document.getElementById('descriptionModal');
        const closeDescriptionBtn = document.querySelector('.close-description');
        const modalDescriptionTitle = document.getElementById('modalDescriptionTitle');
        const modalDescriptionText = document.getElementById('modalDescriptionText');

        document.querySelectorAll('.view-description').forEach(button => {
            button.addEventListener('click', function() {
                const description = this.dataset.description;
                const name = this.dataset.name;
                modalDescriptionTitle.textContent = name;
                modalDescriptionText.textContent = description;
                document.body.style.overflow = 'hidden';
                descriptionModal.style.display = 'block';
                setTimeout(() => descriptionModal.classList.add('show'), 10);
            });
        });

        function closeDescriptionModal() {
            descriptionModal.classList.remove('show');
            setTimeout(() => {
                descriptionModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }, 300);
        }

        closeDescriptionBtn.addEventListener('click', closeDescriptionModal);
        descriptionModal.addEventListener('click', (e) => {
            if (e.target === descriptionModal) closeDescriptionModal();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && descriptionModal.style.display === 'block') closeDescriptionModal();
        });
    });
</script>
@endsection
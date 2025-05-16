@extends('layouts.app')

@section('content')
<style>
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

    /* Стилі для списків у таблиці */
    .table-list {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .table-list li {
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .table-list li:last-child {
        margin-bottom: 0;
    }

    /* Стилі для підсумків */
    .summary {
        margin-top: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #1a73e8;
    }

    /* Стилі для контактних даних */
    .contact-info {
        font-size: 0.875rem;
        color: #6b7280;
    }

    .contact-info strong {
        color: #111827;
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
        from {
            transform: scale(0.9);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
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
        background: rgba(0, 0, 0, 0.5);
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .close:hover {
        background: rgba(255, 255, 255, 0.2);
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
        background: rgba(0, 0, 0, 0.5);
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
        background: rgba(255, 255, 255, 0.2);
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
        background: rgba(0, 0, 0, 0.5);
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

    .dark .table-list li strong,
    .dark .contact-info strong {
        color: #f9fafb;
    }

    .dark .table-list li,
    .dark .contact-info {
        color: #d1d5db;
    }

    .dark .summary {
        color: #60a5fa;
    }
</style>

<div class="container mx-auto px-4 py-8">
    <h1 class="page-title dark:text-white">Замовлення</h1>

    <!-- Повідомлення -->
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <!-- Список бронювань -->
    <h2 class="section-title dark:text-white">Бронювання помешкань</h2>
    @if($bookings->isEmpty())
    <div class="empty-state dark:border-gray-700">
        У вас ще немає бронювань для ваших помешкань.
    </div>
    @else
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th># Бронювання</th>
                    <th>Помешкання</th>
                    <th>Користувач</th>
                    <th>Дата заїзду</th>
                    <th>Дата виїзду</th>
                    <th>Загальна сума</th>
                    <th>Послуги</th>
                    <th>Пакети</th>
                    <th>Тип харчування</th>
                    <th>Контактні дані</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                <tr>
                    <td>#{{ $booking->id }}</td>
                    <td>{{ $booking->accommodation->name ?? 'Невідомо' }}</td>
                    <td>
                        @if($booking->user)
                        {{ $booking->user->name }} ({{ $booking->user->email }})
                        @else
                        Гість ({{ $booking->email }})
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($booking->checkin_date)->format('d.m.Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($booking->checkout_date)->format('d.m.Y') }}</td>
                    <td>{{ number_format($booking->total_price, 2) }} грн</td>
                    <td>
                        @if($booking->services->isNotEmpty())
                        <ul class="table-list">
                            @foreach($booking->services as $bookingService)
                            @if($bookingService->service)
                            <li>{{ $bookingService->service->name }} - <strong>{{ $bookingService->price }} грн</strong></li>
                            @endif
                            @endforeach
                        </ul>
                        <div class="summary">
                            Загалом: {{ $booking->services->sum('price') }} грн
                        </div>
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if($booking->packages->isNotEmpty())
                        <ul class="table-list">
                            @foreach($booking->packages as $bookingPackage)
                            @if($bookingPackage->package)
                            <li>{{ $bookingPackage->package->name }} - <strong>{{ $bookingPackage->price }} грн</strong></li>
                            @endif
                            @endforeach
                        </ul>
                        <div class="summary">
                            Загалом: {{ $booking->packages->sum('price') }} грн
                        </div>
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if($booking->mealOptions->isNotEmpty())
                        <ul class="table-list">
                            @foreach($booking->mealOptions as $mealOption)
                            <li>
                                {{ $mealOption->name ?? 'Невідомий тип' }}
                                ({{ $mealOption->pivot->guests_count ?? 0 }} порцій) -
                                @if($mealOption->pivot->price && $mealOption->pivot->guests_count)
                                <strong>{{ ($mealOption->pivot->price * $mealOption->pivot->guests_count) }} грн</strong>
                                @else
                                Безкоштовно
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        <div class="summary">
                            Загалом порцій: {{ $booking->mealOptions->sum('pivot.guests_count') ?? 0 }}
                        </div>
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        <div class="contact-info">
                            <p><strong>Ім'я:</strong> {{ $booking->name }}</p>
                            <p><strong>Email:</strong> {{ $booking->email }}</p>
                            <p><strong>Телефон:</strong> {{ $booking->phone ?? '-' }}</p>
                            @if($booking->comments)
                            <p><strong>Коментарі:</strong> {{ $booking->comments }}</p>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if(\Carbon\Carbon::parse($booking->checkout_date)->isPast())
                        <span class="status-badge status-rejected">Завершено</span>
                        @else
                        <span class="status-badge status-approved">Активне</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Список замовлених послуг -->
    <h2 class="section-title dark:text-white">Замовлені послуги</h2>
    @if($bookedServices->isEmpty())
    <div class="empty-state dark:border-gray-700">
        У вас ще немає замовлених послуг.
    </div>
    @else
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th># Бронювання</th>
                    <th>Послуга</th>
                    <th>Ціна</th>
                    <th>Помешкання</th>
                    <th>Користувач</th>
                    <th>Дата заїзду</th>
                    <th>Дата виїзду</th>
                    <th>Тип харчування</th>
                    <th>Контактні дані</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookedServices as $bookedService)
                @if($bookedService->booking)
                <tr>
                    <td>#{{ $bookedService->booking->id }}</td>
                    <td>{{ $bookedService->service->name ?? 'Невідома послуга' }}</td>
                    <td>{{ $bookedService->price }} грн</td>
                    <td>{{ $bookedService->booking->accommodation->name ?? 'Невідомо' }}</td>
                    <td>
                        @if($bookedService->booking->user)
                        {{ $bookedService->booking->user->name }} ({{ $bookedService->booking->user->email }})
                        @else
                        Гість ({{ $bookedService->booking->email }})
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($bookedService->booking->checkin_date)->format('d.m.Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($bookedService->booking->checkout_date)->format('d.m.Y') }}</td>
                    <td>
                        @if($bookedService->booking->mealOptions->isNotEmpty())
                        <ul class="table-list">
                            @foreach($bookedService->booking->mealOptions as $mealOption)
                            <li>
                                {{ $mealOption->name ?? 'Невідомий тип' }}
                                ({{ $mealOption->pivot->guests_count ?? 0 }} порцій) -
                                @if($mealOption->pivot->price && $mealOption->pivot->guests_count)
                                <strong>{{ ($mealOption->pivot->price * $mealOption->pivot->guests_count) }} грн</strong>
                                @else
                                Безкоштовно
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        <div class="summary">
                            Загалом порцій: {{ $bookedService->booking->mealOptions->sum('pivot.guests_count') ?? 0 }}
                        </div>
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        <div class="contact-info">
                            <p><strong>Ім'я:</strong> {{ $bookedService->booking->name }}</p>
                            <p><strong>Email:</strong> {{ $bookedService->booking->email }}</p>
                            <p><strong>Телефон:</strong> {{ $bookedService->booking->phone ?? '-' }}</p>
                            @if($bookedService->booking->comments)
                            <p><strong>Коментарі:</strong> {{ $bookedService->booking->comments }}</p>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if(\Carbon\Carbon::parse($bookedService->booking->checkout_date)->isPast())
                        <span class="status-badge status-rejected">Завершено</span>
                        @else
                        <span class="status-badge status-approved">Активне</span>
                        @endif
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
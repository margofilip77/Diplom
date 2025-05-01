@extends('layouts.app')

@section('content')
<div class="container my-5">


    <div class="row">
        <!-- Ліва секція: Меню -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-body">
                    <h3 class="fw-bold mb-4" style="color: #1A1A1A;">Дані користувача</h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item border-0">
                            <a href="#profile-info" class="nav-link active" data-bs-toggle="tab" style="color: #1A73E8;">
                                <i class="fas fa-user me-2"></i> Редагувати профіль
                            </a>
                        </li>
                        <li class="list-group-item border-0">
                            <a href="#bookings" class="nav-link" data-bs-toggle="tab" style="color: #1A1A1A;">
                                <i class="fas fa-ticket-alt me-2"></i> Ваші бронювання
                            </a>
                        </li>
                        <li class="list-group-item border-0">
                            <a href="{{ route('profile.favorites') }}" class="nav-link" style="color: #1A1A1A;">
                                <i class="fas fa-heart me-2 text-red-500 dark:text-red-400"></i> Улюблені
                            </a>
                        </li>

                        <li class="list-group-item border-0">
                            <a href="#password" class="nav-link" data-bs-toggle="tab" style="color: #1A1A1A;">
                                <i class="fas fa-lock me-2"></i> Оновити пароль
                            </a>
                        </li>
                        <li class="list-group-item border-0">
                            <a href="#delete" class="nav-link" data-bs-toggle="tab" style="color: #1A1A1A;">
                                <i class="fas fa-trash-alt me-2"></i> Видалити профіль
                            </a>
                        </li>
                        <li class="list-group-item border-0">
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link" style="color: #FF6200;">
                                <i class="fas fa-sign-out-alt me-2"></i> Вихід
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Права секція: Інформація профілю -->
        <div class="col-md-8">
            <div class="card shadow-sm" style="border-radius: 15px;">
                <div class="card-body">
                    <!-- Повідомлення -->
                    @if(session('status') === 'profile-updated')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Профіль успішно оновлено!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @elseif(session('status') === 'password-updated')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Пароль успішно оновлено!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Вкладки -->
                    <div class="tab-content">
                        <!-- Вкладка: Редагувати профіль -->
                        <!-- Вкладки -->
                        <div class="tab-content">
                            <!-- Вкладка: Редагувати профіль -->
                            <div class="tab-pane fade show active" id="profile-info">
                                <div class="flex justify-center mb-8">
                                    <div class="relative">
                                        @if($user->avatar)
                                        <img src="{{ Storage::url($user->avatar) }}" alt="Аватар" class="w-24 h-24 rounded-full border-4 border-indigo-500 dark:border-indigo-400 shadow-md object-cover transition-transform duration-300 hover:scale-105">
                                        @else
                                        <div class="w-24 h-24 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center border-4 border-indigo-500 dark:border-indigo-400 shadow-md transition-transform duration-300 hover:scale-105">
                                            <span class="text-3xl font-semibold text-gray-500 dark:text-gray-400">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Редагувати профіль</h3>
                                <form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data">
                                    @csrf
                                    @method('patch')

                                    <!-- Ім'я -->
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Ім’я') }}</label>
                                        <input id="name" name="name" type="text" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                                        @if ($errors->has('name'))
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('name') }}</p>
                                        @endif
                                    </div>

                                    <!-- Електронна пошта -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Електронна пошта') }}</label>
                                        <input id="email" name="email" type="email" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" value="{{ old('email', $user->email) }}" required autocomplete="username">
                                        @if ($errors->has('email'))
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('email') }}</p>
                                        @endif

                                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                        <div class="mt-3">
                                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                                {{ __('Адресу електронної пошти не підтверджено.') }}
                                            <form id="send-verification" method="post" action="{{ route('verification.send') }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="underline text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 transition-all duration-200">
                                                    {{ __('Натисніть тут, щоб надіслати листа для підтвердження ще раз.') }}
                                                </button>
                                            </form>
                                            </p>
                                            @if (session('status') === 'verification-link-sent')
                                            <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                                {{ __('Новий лист для підтвердження було надіслано на вашу адресу електронної пошти.') }}
                                            </p>
                                            @endif
                                        </div>
                                        @endif
                                    </div>

                                    <!-- Телефон -->
                                    <div>
                                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Телефон') }}</label>
                                        <input id="phone" name="phone" type="tel" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200" value="{{ old('phone', $user->phone) }}" autocomplete="tel">
                                        @if ($errors->has('phone'))
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('phone') }}</p>
                                        @endif
                                    </div>

                                    <!-- Аватар -->
                                    <div>
                                        <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Оновити аватар') }}</label>
                                        <div class="mt-1">
                                            <input id="avatar" name="avatar" type="file" class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-100 dark:file:bg-indigo-900 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-200 dark:hover:file:bg-indigo-800 transition-all duration-200" accept="image/*">
                                        </div>
                                        @if ($errors->has('avatar'))
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $errors->first('avatar') }}</p>
                                        @endif
                                    </div>

                                    <!-- Кнопка збереження -->
                                    <div class="flex items-center gap-4">
                                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white rounded-lg px-6 py-2 transition-all duration-200">
                                            {{ __('Зберегти') }}
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Вкладка: Ваші бронювання -->
                            <div class="tab-pane fade" id="bookings">
                                <h3 class="fw-bold mb-4" style="color: #1A1A1A;">Ваші бронювання</h3>
                                <p class="text-muted">Тут відображатимуться ваші бронювання (в розробці).</p>
                            </div>
                            <!-- Вкладка: Улюблені -->
                            <div class="tab-pane fade" id="favorites">
                                <h3 class="fw-bold mb-4" style="color: #1A1A1A;">Улюблені</h3>
                                @if($user->favorites->isEmpty())
                                <p class="text-muted">У вас поки немає улюблених об’єктів.</p>
                                @else
                                <div class="row">
                                    @foreach($user->favorites as $favorite)
                                    @php
                                    $accommodation = $favorite->accommodation;
                                    $mainPhoto = $accommodation->photos->first()->photo_path ?? 'images/default.jpg';
                                    @endphp
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100" style="border-radius: 10px;">
                                            <img src="{{ asset($mainPhoto) }}" class="card-img-top" alt="{{ $accommodation->name }}" style="height: 150px; object-fit: cover;">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    {{ $accommodation->name }}
                                                </h5>
                                                <p class="text-muted">
                                                    <span class="material-icons icon">location_on</span>
                                                    {{ $accommodation->settlement }}, {{ $accommodation->region }}
                                                </p>
                                                <p class="text-truncate description">{{ $accommodation->description }}</p>
                                                <p class="price">{{ $accommodation->price_per_night }} грн/ніч</p>
                                                <p class="text-muted d-flex align-items-center mt-2">
                                                    <span class="material-icons text-warning">star</span>
                                                    <span class="fw-bold mx-1">{{ number_format($accommodation->average_rating, 1) }}</span>
                                                    <span class="text-secondary">({{ $accommodation->reviews_count }} відгуків)</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <!-- Вкладка: Оновлення пароля -->
                            <div class="tab-pane fade" id="password">
                                <h3 class="fw-bold mb-4" style="color: #1A1A1A;">Оновлення пароля</h3>
                                @include('profile.partials.update-password-form')
                            </div>

                            <!-- Вкладка: Видалити профіль -->
                            <div class="tab-pane fade" id="delete">
                                <h3 class="fw-bold mb-4" style="color: #1A1A1A;">Видалити профіль</h3>
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .nav-link {
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #1A73E8 !important;
        }

        .nav-link.active {
            background: none !important;
            color: #1A73E8 !important;
            font-weight: 600;
        }

        .list-group-item {
            padding: 0.75rem 0;
        }

        .card {
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #E5E7EB;
            padding: 10px;
        }

        .form-control:focus {
            border-color: #1A73E8;
            box-shadow: 0 0 5px rgba(26, 115, 232, 0.3);
        }

        .btn {
            border-radius: 25px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
    @endsection
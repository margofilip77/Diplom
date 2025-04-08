<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Зелений Туризм</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Встав у <head> -->
<meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">

</head>

<body>
    <!-- 1. Хедер -->
    <header class="bg-light py-3">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="logo">
                <h1><a href="/" class="text-dark text-decoration-none">Зелений Шлях</a></h1>
            </div>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a href="/" class="nav-link">Головна</a></li>
                    <li class="nav-item"><a href="{{ route('services') }}" class="nav-link">Послуги</a></li>
                    <li class="nav-item"><a href="{{ route('accommodations.accommodation') }}" class="nav-link">Помешкання</a></li>
                    <li class="nav-item"><a href="/contacts" class="nav-link">Контакти</a></li>
                    <li class="nav-item">
                    <a class="nav-link" href="{{ Auth::check() && Auth::user()->cart ? route('cart.show', Auth::user()->cart->id) : '#' }}">
    <i class="fas fa-shopping-cart"></i> Кошик
    <span class="badge bg-danger">
        {{ Auth::check() && Auth::user()->cartItems ? Auth::user()->cartItems->count() : 0 }}
    </span>
</a>
                    </li>

                </ul>
            </nav>
            <div class="auth-buttons">
                <a href="/login" class="btn btn-outline-dark me-2">Вхід</a>
                <a href="/register" class="btn btn-dark">Реєстрація</a>
            </div>
        </div>
    </header>

    {{-- Контент сторінки --}}




    <div class="container mt-4">
        @yield('content')
    </div>



    <!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">


</body>

</html>
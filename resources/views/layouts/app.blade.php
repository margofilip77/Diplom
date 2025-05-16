<!DOCTYPE html>
<html lang="uk">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Зелений Шлях - @yield('title', 'Екологічний туризм')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Підключення шрифту Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1A73E8;
            --secondary: #F7F7F7;
            --accent: #FF6200;
            --text-dark: #1A1A1A;
            --text-light: #FFFFFF;
            --neutral: #6B7280;
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            background-color: var(--secondary);
            color: var(--text-dark);
        }

        .navbar {
            background: var(--text-light);
            padding: 0.5rem 0;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1050 !important;
            min-height: 60px;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary) !important;
            transition: color 0.3s ease;
            padding: 0 1rem;
        }

        .navbar-brand:hover {
            color: var(--accent) !important;
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 400;
            padding: 0.5rem 1rem;
            white-space: nowrap;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary) !important;
            transform: translateY(-1px);
        }

        .nav-item.cart .badge {
            font-size: 0.65rem;
            padding: 0.25em 0.5em;
            background: var(--accent);
            position: absolute;
            top: -5px;
            right: -5px;
        }

        .dropdown-menu {
            min-width: 200px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background: var(--text-light);
        }

        .dropdown-item {
            padding: 0.5rem 1.5rem;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: var(--secondary);
            color: var(--primary);
        }

        .auth-buttons .btn {
            border-radius: 30px;
            padding: 0.25rem 1rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            margin-left: 0.5rem;
        }

        .auth-buttons .btn-outline-primary {
            border-color: var(--primary);
            color: var(--primary);
        }

        .auth-buttons .btn-outline-primary:hover {
            background: var(--primary);
            color: var(--text-light);
            transform: translateY(-2px);
        }

        .auth-buttons .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--text-light);
        }

        .auth-buttons .btn-primary:hover {
            background: #1557B0;
            border-color: #1557B0;
            transform: translateY(-2px);
        }

        .footer {
            background: var(--text-dark);
            color: var(--text-light);
            padding: 3rem 0;
            font-size: 0.95rem;
        }

        .footer h5 {
            font-weight: 600;
            color: var(--text-light);
            margin-bottom: 1.5rem;
        }

        .footer a {
            color: #B0B0B0;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: var(--accent);
        }

        .footer .social-icons a {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: #B0B0B0;
            transition: all 0.3s ease;
        }

        .footer .social-icons a:hover {
            color: var(--accent);
            transform: translateY(-3px);
        }

        .footer .text-muted {
            color: #6B7280 !important;
        }

        @media (max-width: 991px) {
            .navbar-nav {
                background: var(--secondary);
                padding: 1rem;
                border-radius: 8px;
                margin-top: 0.5rem;
            }

            .navbar-collapse {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--text-light);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                padding: 1rem;
                transition: all 0.3s ease;
            }

            .auth-buttons {
                margin-top: 1rem;
                justify-content: center;
                flex-wrap: wrap;
            }

            .auth-buttons .btn {
                margin: 0.25rem;
                width: 100%;
                max-width: 120px;
            }

            .dropdown-menu {
                position: static !important;
                float: none;
                width: 100%;
                box-shadow: none;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.5rem;
            }

            .nav-link {
                padding: 0.5rem 0.8rem;
                font-size: 0.9rem;
            }

            .auth-buttons .btn {
                font-size: 0.85rem;
                padding: 0.25rem 0.75rem;
            }
        }
    </style>
</head>

<body>
    <!-- Навігація -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">Зелений Шлях</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Головна</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Каталог
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('services.index') }}">Послуги</a></li>
                            <li><a class="dropdown-item" href="{{ route('accommodations.index') }}">Помешкання</a></li>
                            <li><a class="dropdown-item" href="{{ route('contact.form') }}">Контакти</a></li>
                        </ul>
                    </li>
                    @if (Auth::check())
                    @if (Auth::user()->is_blocked)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact.form') }}">Звернутися до служби підтримки</a>
                    </li>
                    @else
                    <li class="nav-item cart position-relative">
                        <a class="nav-link" href="{{ route('cart.show') }}">
                            <i class="fas fa-shopping-cart"></i> Бронювання
                            @if(Auth::check() && Auth::user()->cart && Auth::user()->cart->accommodations->count() > 0)
                            <span class="badge">{{ Auth::user()->cart->accommodations->count() }}</span>
                            @elseif(!Auth::check() && count(Session::get('cart', [])) > 0)
                            <span class="badge">{{ count(Session::get('cart', [])) }}</span>
                            @endif
                        </a>
                    </li>
                    @if(Auth::user()->role !== 'provider')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('provider.register') }}">
                            <i class="fas fa-plus-circle me-1"></i> Зареєструвати помешкання/послугу
                        </a>
                    </li>
                    @endif
                    @if(Auth::user()->role === 'admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Адмін-панель</a>
                    </li>
                    @endif
                    @if(Auth::user()->role === 'provider')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('provider.dashboard') }}">Панель надавача</a>
                    </li>
                    @endif
                    @endif
                    @else
                    <li class="nav-item cart position-relative">
                        <a class="nav-link" href="{{ route('cart.show') }}">
                            <i class="fas fa-shopping-cart"></i> Бронювання
                            @if(count(Session::get('cart', [])) > 0)
                            <span class="badge">{{ count(Session::get('cart', [])) }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                            <a class="nav-link" href="{{ route('provider.auth') }}">
                                <i class="fas fa-plus-circle me-1"></i> Зареєструвати помешкання/послугу
                            </a>
                        </li>
                    @endif
                </ul>
                <div class="auth-buttons ms-3 d-flex align-items-center">
                    @if(Auth::check())
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-user me-1"></i> Профіль
                    </a>
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-primary">Вихід</a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Вхід</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Реєстрація</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    <!-- Повідомлення про блокування -->
    @if (Auth::check() && Auth::user()->is_blocked)
    <div class="container mt-3">
        <div class="alert alert-danger text-center">
            Ваш обліковий запис заблоковано. Ви можете переглядати сторінки, але не можете виконувати більшість дій.
            <form action="{{ route('contact.form') }}" method="GET" style="display:inline;">
                <button type="submit" class="alert-link btn btn-link p-0">Зверніться до служби підтримки</button>
            </form>
        </div>
    </div>
    @endif
    <!-- Контент -->
    <main class="container mt-5">
        @yield('content')
    </main>

    <!-- Футер -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5>Зелений Шлях</h5>
                    <p class="text-muted">Екологічний туризм для відпочинку та натхнення. Досліджуйте природу України разом з нами!</p>
                </div>
                <div class="col-md-2">
                    <h5>Навігація</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ url('/') }}">Головна</a></li>
                        <li><a href="{{ route('services.index') }}">Послуги</a></li>
                        <li><a href="{{ route('accommodations.index') }}">Помешкання</a></li>
                        <li><a href="{{ url('/contacts') }}">Контакти</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Контакти</h5>
                    <p class="text-muted">
                        Email: <a href="mailto:info@greenpath.com">info@greenpath.com</a><br>
                        Телефон: +38 (099) 123-45-67
                    </p>
                </div>
                <div class="col-md-3">
                    <h5>Слідкуйте за нами</h5>
                    <div class="social-icons">
                        <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="text-muted">© {{ date('Y') }} Зелений Шлях. Всі права захищені.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Додаткові скрипти -->
    @yield('scripts')
</body>

</html>
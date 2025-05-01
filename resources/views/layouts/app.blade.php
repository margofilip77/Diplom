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
        /* Загальні стилі */
        :root {
            --primary: #1A73E8;
            /* Синій, як у Booking.com */
            --secondary: #F7F7F7;
            /* Світло-сірий фон */
            --accent: #FF6200;
            /* Помаранчевий акцент, як у Expedia */
            --text-dark: #1A1A1A;
            /* Темно-сірий текст */
            --text-light: #FFFFFF;
            /* Білий текст */
            --neutral: #6B7280;
            /* Нейтральний сірий */
        }

        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            background-color: var(--secondary);
            color: var(--text-dark);
        }

        /* Навігація */
        .navbar {
            background: var(--text-light);
            padding: 1rem 0;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-brand {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--primary) !important;
            transition: color 0.3s ease;
        }

        .navbar-brand:hover {
            color: var(--accent) !important;
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 400;
            padding: 0.5rem 1.2rem;
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
        }

        .auth-buttons .btn {
            border-radius: 30px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
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
            /* Темніший синій */
            border-color: #1557B0;
            transform: translateY(-2px);
        }

        /* Футер */
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

        /* Адаптивність */
        @media (max-width: 991px) {
            .navbar-nav {
                background: var(--secondary);
                padding: 1rem;
                border-radius: 8px;
                margin-top: 0.5rem;
            }

            .auth-buttons {
                margin-top: 1rem;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 1.5rem;
            }

            .nav-link {
                padding: 0.5rem 0.8rem;
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
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/') }}">Головна</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('services.index') }}">Послуги</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('accommodations.index') }}">Помешкання</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact.form') }}">Контакти</a>
                </li>
                @if (Auth::check())
                    @if (Auth::user()->is_blocked)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact.form') }}">Звернутися до служби підтримки</a>
                        </li>
                    @else
                        <li class="nav-item cart">
                            <a class="nav-link" href="{{ route('cart.show') }}">
                                <i class="fas fa-shopping-cart"></i> Бронювання
                                <span class="badge">
                                    @if(Auth::check())
                                        {{ Auth::user()->cart ? Auth::user()->cart->accommodations->count() : 0 }}
                                    @else
                                        {{ count(Session::get('cart', [])) }}
                                    @endif
                                </span>
                            </a>
                        </li>
                        <!-- Додаємо навігацію для адмін-панелі, якщо користувач є адміном -->
                        @if(Auth::user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">Адмін-панель</a>
                            </li>
                        @endif
                        <!-- Додаємо навігацію для надавачів, якщо користувач є надавачем -->
                        @if(Auth::user()->role === 'provider')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('provider.dashboard') }}">Панель надавача</a>
                            </li>
                        @endif
                    @endif
                @else
                    <li class="nav-item cart">
                        <a class="nav-link" href="{{ route('cart.show') }}">
                            <i class="fas fa-shopping-cart"></i> Бронювання
                            <span class="badge">
                                {{ count(Session::get('cart', [])) }}
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
            <div class="auth-buttons ms-3 d-flex gap-2">
                @if(Auth::check())
                    <!-- Додаємо посилання на профіль -->
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
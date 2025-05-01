<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адмін-панель</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Адмін-панель</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.users') }}">Користувачі</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.reviews') }}">Відгуки</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.pending-offers') }}">Пропозиції</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.packages') }}">Пакети</a>
                    </li>
                </ul>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Вийти</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
                <a href="{{ route('home') }}" class="btn btn-primary mt-2">Повернутися на головну</a>
            </div>
        @else
            <h1>Адмін-панель</h1>
            <p>Ласкаво просимо до адмін-панелі! Виберіть розділ у меню для управління.</p>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вибір авторизації</title>
    <!-- Підключення Bootstrap -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Оформлення замовлення</h4>
                    </div>
                    <div class="card-body text-center">
                        @if (session('info'))
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                {{ session('info') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <p>Щоб продовжити оформлення замовлення, будь ласка, увійдіть у свій обліковий запис або зареєструйтесь.</p>

                        <div class="d-flex justify-content-center">
                            <a href="{{ route('login') }}" class="btn btn-primary me-3">Увійти</a>
                            <a href="{{ route('register') }}" class="btn btn-success">Зареєструватись</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Підключення Bootstrap JS -->
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
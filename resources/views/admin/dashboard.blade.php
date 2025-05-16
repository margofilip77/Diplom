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
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.support-messages') }}">Служба підтримки</a>
                    </li>
                </ul>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-danger">Вийти</button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Модальне вікно для сповіщення -->
    @if($hasUnviewedMessages)
        <div class="modal fade" id="unviewedMessagesModal" tabindex="-1" aria-labelledby="unviewedMessagesModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="unviewedMessagesModalLabel">Нагадування</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>У вас є непрочитані повідомлення, які потребують вашої відповіді!</p>
                        <p><a href="{{ route('admin.support-messages') }}" class="btn btn-primary">Переглянути повідомлення</a></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('unviewedMessagesModal'));
                modal.show();

                // Періодичне нагадування кожні 5 хвилин
                setInterval(() => {
                    fetch('/admin/check-unviewed-messages')
                        .then(response => response.json())
                        .then(data => {
                            if (data.hasUnviewedMessages) {
                                showToast('У вас є непрочитані повідомлення, які потребують відповіді!', 'warning');
                            }
                        });
                }, 300000); // 5 хвилин = 300000 мс

                // Корекція часу показу тосту до 3 секунд
                function showToast(message, type = 'warning') {
                    // Перевірка, чи контейнер уже існує, щоб уникнути дублювання
                    let toastContainer = document.getElementById('toast-container');
                    if (!toastContainer) {
                        toastContainer = document.createElement('div');
                        toastContainer.id = 'toast-container';
                        toastContainer.style = 'position: fixed; top: 20px; right: 20px; z-index: 1000;';
                        document.body.appendChild(toastContainer);
                    }

                    const toast = document.createElement('div');
                    toast.className = `toast-notification ${type}`;
                    toast.innerHTML = `<span>${message}</span>`;
                    toastContainer.appendChild(toast);
                    setTimeout(() => toast.classList.add('show'), 100);
                    setTimeout(() => {
                        toast.classList.remove('show');
                        setTimeout(() => toast.remove(), 300);
                    }, 3000); // 3 секунди = 3000 мс
                }
            });
        </script>

        <style>
            .toast-notification {
                background: #ffc107;
                color: #333;
                padding: 15px 20px;
                border-radius: 8px;
                margin-bottom: 10px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease-in-out;
            }

            .toast-notification.show {
                opacity: 1;
                transform: translateX(0);
            }
        </style>
    @endif

    <div class="container mt-4">
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
                <a href="{{ route('home') }}" class="btn btn-primary mt-2">Повернутися на головну</a>
            </div>
        @else
            <h1>Адмін-панель</h1>
            <p>Ласкаво просимо до адмін-панелі! Виберіть розділ у меню для управління.</p>
            <a href="{{ route('admin.support-messages') }}" class="btn btn-primary mt-3">Перейти до повідомлень</a>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
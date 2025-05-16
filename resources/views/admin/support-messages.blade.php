@extends('layouts.app')

@section('title', 'Повідомлення служби підтримки')

@section('content')
<div class="container mt-4">
    <h1>Повідомлення служби підтримки</h1>

    <!-- Фільтрація -->
    <div class="mb-3">
        <form method="GET" action="{{ route('admin.support-messages') }}" class="d-flex align-items-center">
            <label for="filter" class="me-2">Фільтрувати:</label>
            <select name="filter" id="filter" class="form-select w-auto me-2" onchange="this.form.submit()">
                <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Усі</option>
                <option value="recent" {{ $filter == 'recent' ? 'selected' : '' }}>Недавно (до 24 годин)</option>
                <option value="older" {{ $filter == 'older' ? 'selected' : '' }}>Пізніше (старше 24 годин)</option>
            </select>
        </form>
    </div>

    <!-- Перелік повідомлень -->
    @if($messages->isEmpty())
    <p>Повідомлень немає.</p>
    @else
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ім'я</th>
                <th>Email</th>
                <th>Повідомлення</th>
                <th>Дата</th>
                <th>Дата відповіді</th>
                <th>Відповідь</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @foreach($messages as $message)
            <tr>
                <td>{{ $message->name }}</td>
                <td>{{ $message->email }}</td>
                <td>{{ $message->message }}</td>
                <td>{{ $message->created_at->format('d.m.Y H:i') }}</td>
                <td>{{ $message->responded_at ? $message->responded_at->format('d.m.Y H:i') : 'Ще не відповіли' }}</td>
                <td>{{ $message->response ?? 'Відповідь ще не надана' }}</td>
                <td>
                    @if(!$message->response)
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#respondModal{{ $message->id }}">
                        Відповісти
                    </button>
                    @else
                    <span>Відповідь надана</span>
                    @endif

                    <!-- Кнопка для видалення -->
                    <form action="{{ route('admin.support-messages.delete', $message) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ви впевнені, що хочете видалити це повідомлення?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger ms-2">Видалити</button>
                    </form>

                    <!-- Модальне вікно для відповіді -->
                    @if(!$message->response)
                    <div class="modal fade" id="respondModal{{ $message->id }}" tabindex="-1" aria-labelledby="respondModalLabel{{ $message->id }}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="respondModalLabel{{ $message->id }}">Відповідь на повідомлення</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.support-messages.respond', $message) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="response">Ваша відповідь</label>
                                            <textarea class="form-control" id="response" name="response" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Скасувати</button>
                                        <button type="submit" class="btn btn-primary">Надіслати</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $messages->links() }}
    @endif

    <!-- Модальне вікно для сповіщення про непрочитані повідомлень -->
    <div class="modal fade" id="unviewedMessagesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Нагадування</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>У вас є непрочитані повідомлення, які потребують вашої відповіді!</p>
                    <div id="unviewedMessagesList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
                    <a href="{{ route('admin.support-messages') }}" class="btn btn-primary">Переглянути всі</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Модальне вікно для сповіщення про нові недавні повідомлення -->
    <div class="modal fade" id="recentMessagesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Нові повідомлення</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>У вас є нові повідомлення за останні 24 години, які потребують вашої відповіді!</p>
                    <div id="recentMessagesList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
                    <a href="{{ route('admin.support-messages') }}" class="btn btn-primary">Переглянути всі</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast для нагадування -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>
</div>

<!-- Підключення Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Перевірка непрочитаних повідомлень
        fetch('/admin/check-unviewed-messages')
            .then(response => response.json())
            .then(data => {
                if (data.hasUnviewedMessages) {
                    const modal = new bootstrap.Modal(document.getElementById('unviewedMessagesModal'));
                    modal.show();

                    // Відображаємо список непрочитаних повідомлень у модальному вікні
                    const unviewedMessagesList = document.getElementById('unviewedMessagesList');
                    unviewedMessagesList.innerHTML = '';
                    data.messages.forEach(message => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'border-bottom py-2';
                        messageDiv.innerHTML = `
                        <p><strong>${message.name}</strong> (${new Date(message.created_at).toLocaleString('uk-UA')}): ${message.message}</p>
                        <button class="btn btn-sm btn-danger delete-message" data-message-id="${message.id}">Видалити</button>
                    `;
                        unviewedMessagesList.appendChild(messageDiv);
                    });

                    // Додаємо обробник для кнопок видалення
                    document.querySelectorAll('.delete-message').forEach(button => {
                        button.addEventListener('click', function() {
                            const messageId = this.dataset.messageId;
                            if (confirm('Ви впевнені, що хочете видалити це повідомлення?')) {
                                fetch(`/admin/support-messages/${messageId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            this.parentElement.remove();
                                            showToast('Повідомлення видалено!', 'success');
                                            // Якщо більше немає повідомлень, закриваємо модальне вікно
                                            if (!unviewedMessagesList.children.length) {
                                                modal.hide();
                                            }
                                        } else {
                                            showToast('Помилка при видаленні повідомлення', 'danger');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showToast('Помилка при видаленні повідомлення', 'danger');
                                    });
                            }
                        });
                    });

                    // Періодична перевірка непрочитаних повідомлень
                    setInterval(() => {
                        fetch('/admin/check-unviewed-messages')
                            .then(response => response.json())
                            .then(data => {
                                if (data.hasUnviewedMessages) {
                                    showToast('Непрочитані повідомлення потребують вашої відповіді!', 'warning');
                                }
                            });
                    }, 5000); // 5 секунд
                }
            });

        // Перевірка нових недавніх повідомлень (до 24 годин)
        fetch('/admin/check-recent-messages')
            .then(response => response.json())
            .then(data => {
                if (data.hasRecentMessages) {
                    const modal = new bootstrap.Modal(document.getElementById('recentMessagesModal'));
                    modal.show();

                    // Відображаємо список недавніх повідомлень у модальному вікні
                    const recentMessagesList = document.getElementById('recentMessagesList');
                    recentMessagesList.innerHTML = '';
                    data.messages.forEach(message => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'border-bottom py-2';
                        messageDiv.innerHTML = `
                        <p><strong>${message.name}</strong> (${new Date(message.created_at).toLocaleString('uk-UA')}): ${message.message}</p>
                        <button class="btn btn-sm btn-danger delete-message" data-message-id="${message.id}">Видалити</button>
                    `;
                        recentMessagesList.appendChild(messageDiv);
                    });

                    // Додаємо обробник для кнопок видалення
                    document.querySelectorAll('.delete-message').forEach(button => {
                        button.addEventListener('click', function() {
                            const messageId = this.dataset.messageId;
                            if (confirm('Ви впевнені, що хочете видалити це повідомлення?')) {
                                fetch(`/admin/support-messages/${messageId}`, {
                                        method: 'DELETE',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        }
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            this.parentElement.remove();
                                            showToast('Повідомлення видалено!', 'success');
                                            // Якщо більше немає повідомлень, закриваємо модальне вікно
                                            if (!recentMessagesList.children.length) {
                                                modal.hide();
                                            }
                                        } else {
                                            showToast('Помилка при видаленні повідомлення', 'danger');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        showToast('Помилка при видаленні повідомлення', 'danger');
                                    });
                            }
                        });
                    });

                    // Періодична перевірка нових недавніх повідомлень
                    setInterval(() => {
                        fetch('/admin/check-recent-messages')
                            .then(response => response.json())
                            .then(data => {
                                if (data.hasRecentMessages) {
                                    showToast('Нові повідомлення за останні 24 години потребують вашої відповіді!', 'info');
                                }
                            });
                    }, 5000); // 5 секунд
                }
            });

        function showToast(message, type = 'warning') {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast-notification ${type}`;
            toast.innerHTML = `<span>${message}</span>`;
            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    });
</script>

<style>
    .toast-notification {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease-in-out;
    }

    .toast-notification.warning {
        background: #ffc107;
        color: #333;
    }

    .toast-notification.info {
        background: #17a2b8;
        color: #fff;
    }

    .toast-notification.success {
        background: #28a745;
        color: #fff;
    }

    .toast-notification.danger {
        background: #dc3545;
        color: #fff;
    }

    .toast-notification.show {
        opacity: 1;
        transform: translateX(0);
    }
</style>
@endsection
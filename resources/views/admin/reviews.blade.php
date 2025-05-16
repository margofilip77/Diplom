@extends('layouts.app')

@section('title', 'Відгуки - Адмін-панель')

@section('content')
<div class="container mt-4">
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
        <a href="{{ route('home') }}" class="btn btn-primary mt-2">Повернутися на головну</a>
    </div>
    @else
    <h1>Відгуки</h1>
    @if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif
    <table class="table table-striped mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>Користувач</th>
                <th>Помешкання</th>
                <th>Коментар</th>
                <th>Рейтинг</th>
                <th>Дата створення</th>
                <th>Дата блокування</th>
                <th>Статус</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reviews as $review)
            <tr>
                <td>{{ $review->id }}</td>
                <td>{{ $review->user->name }}</td>
                <td>{{ $review->accommodation ? $review->accommodation->name : 'Немає' }}</td>
                <td>{{ $review->comment }}</td>
                <td>{{ $review->rating }}</td>
                <td>{{ $review->created_at->format('d.m.Y H:i') }}</td>
                <td>{{ $review->blocked_at ? (\Carbon\Carbon::parse($review->blocked_at)->format('d.m.Y H:i')) : '-' }}</td>
                <td>{{ $review->is_blocked ? 'Заблоковано' : 'Опубліковано' }}</td>
                <td>
                    <form action="{{ route('admin.reviews.block', $review) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ви впевнені, що хочете {@if ($review->is_blocked) розблокувати @else заблокувати @endif цей відгук?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm {{ $review->is_blocked ? 'btn-success' : 'btn-warning' }}">
                            {{ $review->is_blocked ? 'Розблокувати' : 'Заблокувати' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.reviews.delete', $review) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ви впевнені, що хочете видалити цей відгук?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger ml-2">Видалити</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
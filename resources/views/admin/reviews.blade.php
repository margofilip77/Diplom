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
                        <td>
                            <form action="{{ route('admin.reviews.delete', $review) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ви впевнені, що хочете видалити цей відгук?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Видалити</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
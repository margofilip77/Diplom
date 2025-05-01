@extends('layouts.app')

@section('title', 'Пакети (Адмін)')

@section('content')
<div class="container mt-4">
    <h1>Пакети</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">Створити новий пакет</a>
    </div>

    @if ($packages->isEmpty())
        <p>Немає створених пакетів.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Назва</th>
                    <th>Опис</th>
                    <th>Знижка (%)</th>
                    <th>Ціна (грн)</th>
                    <th>Послуги</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packages as $package)
                    <tr>
                        <td>{{ $package->name }}</td>
                        <td>{{ $package->description ?? 'Немає опису' }}</td>
                        <td>{{ $package->discount }}</td>
                        <td>{{ $package->price }}</td>
                        <td>
                            <ul style="list-style-type: none; padding-left: 0;">
                                @foreach ($package->services as $service)
                                    <li class="flex items-center mb-1">
                                        @if ($service->image)
                                            <img src="{{ asset('services/' . $service->image) }}" alt="{{ $service->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 8px;">
                                        @else
                                            <img src="https://via.placeholder.com/40" alt="Немає фото" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 8px;">
                                        @endif
                                        <span>{{ $service->name }} ({{ $service->price }} грн)</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-sm btn-warning">Редагувати</a>
                            <form action="{{ route('admin.packages.delete', $package->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити цей пакет?')">Видалити</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
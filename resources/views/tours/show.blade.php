@extends('layouts.app')

@section('content')

<div class="container mt-5">
    <!-- Назва туру -->
    <h1>{{ $tour->name }}</h1>

    <!-- Зображення туру -->
    <div class="mb-4">
        <img src="{{ asset('storage/' . $tour->image) }}" alt="{{ $tour->name }}" class="img-fluid rounded">
    </div>

    <!-- Опис туру -->
    <div class="mb-4">
        <p><strong>Опис:</strong></p>
        <p>{{ $tour->description }}</p>
    </div>

    <!-- Ціна туру -->
    <div class="mb-4">
        <p><strong>Ціна:</strong> {{ $tour->price }} грн</p>
    </div>

    <!-- Дата проведення -->
    <div class="mb-4">
        <p><strong>Дата проведення:</strong> {{ $tour->date }}</p>
    </div>

    <!-- Кнопка для бронювання туру -->
    <div>
        <a href="{{ route('booking.create', $tour->id) }}" class="btn btn-primary">Забронювати тур</a>
    </div>
</div>

@endsection

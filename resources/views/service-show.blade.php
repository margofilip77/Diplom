@extends('layouts.app')

@section('content')
<div class="container my-5">
    <h1>{{ $service->name }}</h1>
    <img src="{{ $service->image }}" alt="{{ $service->name }}" class="img-fluid">
    <p>{{ $service->description }}</p>
    <p><strong>Ціна:</strong> {{ $service->price }} грн</p>
    <a href="{{ route('services') }}" class="btn btn-secondary">Назад до послуг</a>
</div>
@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@endsection

@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5 fw-bold">Улюблені помешкання</h1>

    @if($favorites->isEmpty())
        <div class="text-center">
            <p class="text-muted fs-5">У вас поки немає улюблених помешкань.</p>
            <a href="{{ route('accommodations.index') }}" class="btn btn-outline-success mt-3">Переглянути всі помешкання</a>
    @else
        <div class="row g-4">
            @foreach($favorites as $favorite)
                @php
                    $accommodation = $favorite->accommodation;
                    $mainPhoto = $accommodation->photos->first()->photo_path ?? 'images/default.jpg';
                @endphp
                <div class="col-12 col-sm-6 col-md-4">
                    <a href="{{ route('accommodations.show', $accommodation->id) }}" class="text-decoration-none">
                        <div class="card h-100 shadow rounded-4 border-0">
                            <img src="{{ asset($mainPhoto) }}" class="card-img-top rounded-top-4" alt="{{ $accommodation->name }}">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <h5 class="card-title text-dark">{{ $accommodation->name }}</h5>
                                <p class="mb-1 text-secondary">
                                    <i class="material-icons icon align-middle">location_on</i>
                                    {{ $accommodation->settlement }}, {{ $accommodation->region }}
                                </p>
                                <p class="text-muted description mb-1">{{ $accommodation->description }}</p>
                                <p class="price mb-2">{{ $accommodation->price_per_night }} грн/ніч</p>
                                <div class="d-flex align-items-center text-muted">
                                    <i class="material-icons text-warning me-1">star</i>
                                    <span class="fw-semibold me-1">{{ number_format($accommodation->average_rating, 1) }}</span>
                                    <span>({{ $accommodation->reviews_count }} відгуків)</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
    .card-img-top {
        height: 200px;
        object-fit: cover;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .description {
        font-size: 0.9rem;
        height: 2.8em;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .price {
        font-size: 1.1rem;
        font-weight: bold;
        color: #28a745;
    }

    .material-icons {
        font-size: 18px;
    }

    .icon {
        vertical-align: middle;
    }
</style>

<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
@endsection

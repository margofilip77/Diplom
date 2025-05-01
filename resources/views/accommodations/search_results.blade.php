@forelse($accommodations as $accommodation)
    <div class="col-md-4 mb-4">
        <a href="{{ route('accommodations.show', $accommodation->id) }}">
            <div class="card shadow-sm position-relative">
                @php
                    $mainPhoto = $accommodation->photos->first()->photo_path ?? 'images/default.jpg';
                    $isFavorited = auth()->check() && auth()->user()->hasFavorited($accommodation->id);
                @endphp

                <img src="{{ asset($mainPhoto) }}" class="card-img-top" alt="{{ $accommodation->name }}">

                @if(auth()->check())
                    <button class="btn like-button position-absolute top-0 end-0 m-2 toggle-favorite"
                            data-accommodation-id="{{ $accommodation->id }}"
                            data-is-favorited="{{ $isFavorited ? 'true' : 'false' }}">
                        <span class="material-icons like-icon {{ $isFavorited ? 'text-danger' : 'text-white' }}">
                            {{ $isFavorited ? 'favorite' : 'favorite_border' }}
                        </span>
                    </button>
                @endif

                <div class="card-body">
                    <h5 class="card-title">{{ $accommodation->name }}</h5>
                    <p class="text-muted">
                        <span class="material-icons icon">location_on</span>
                        {{ $accommodation->settlement }}, {{ $accommodation->region }}
                    </p>
                    <p class="description">{{ $accommodation->description }}</p>
                    <p class="price">{{ $accommodation->price_per_night }} грн/ніч</p>
                    <p class="text-muted d-flex align-items-center mt-2">
                        <span class="material-icons text-warning">star</span>
                        <span class="fw-bold mx-1">{{ number_format($accommodation->average_rating, 1) }}</span>
                        <span class="text-secondary">({{ $accommodation->reviews_count }} відгуків)</span>
                    </p>
                </div>
            </div>
        </a>
    </div>
@empty
    <p class="text-muted">Нічого не знайдено за вашим запитом.</p>
@endforelse
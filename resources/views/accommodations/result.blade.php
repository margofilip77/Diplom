@if($accommodations->count())
    <div class="row">
        @foreach ($accommodations as $accommodation)
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <img src="{{ $accommodation->image }}" class="card-img-top" alt="Фото">
                    <div class="card-body">
                        <h5 class="card-title">{{ $accommodation->title }}</h5>
                        <p class="card-text">📍 {{ $accommodation->location }}</p>
                        <p class="card-text">👥 Максимум {{ $accommodation->max_guests }} гостей</p>
                        <p class="card-text">💰 Ціна: {{ $accommodation->price }} грн/ніч</p>
                        <a href="{{ route('accommodation.show', $accommodation->id) }}" class="btn btn-primary">Детальніше</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-center text-muted">❌ Нічого не знайдено</p>
@endif

@if($accommodations->isEmpty())
    <p class="text-center text-muted">Нічого не знайдено 😔</p>
@else
    <div class="row">
        @foreach($accommodations as $accommodation)
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <img src="{{ asset($accommodation->image) }}" class="card-img-top" alt="{{ $accommodation->name }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $accommodation->name }}</h5>
                        <p class="card-text">{{ $accommodation->location }}</p>
                        <p class="text-muted">💰 Ціна: {{ $accommodation->price_per_night }} грн/ніч</p>
                        <a href="{{ route('accommodations.show', $accommodation->id) }}" class="btn btn-primary">Детальніше</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

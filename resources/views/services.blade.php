<section class="container my-5">
    <h3 class="text-center mb-4" data-aos="fade-up">Популярні послуги</h3>
    @if(isset($services) && count($services) > 0)
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach ($services as $service)
                <div class="col">
                    <div class="card service-card">
                        <img src="{{ $service->image }}" class="card-img-top" alt="{{ $service->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $service->name }}</h5>
                            <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                            <p class="fw-bold">Ціна: {{ $service->price }} грн</p>
                            <!-- Посилання на сторінку конкретної послуги -->
                            <a href="{{ route('service.show', $service->id) }}" class="btn btn-primary w-100">Детальніше</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center">Немає доступних послуг.</p>
    @endif
</section>

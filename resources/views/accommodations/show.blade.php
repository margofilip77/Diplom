@if($accommodation->photos->isNotEmpty())
    @php
        $mainPhoto = $accommodation->photos->first();
    @endphp
    <div class="main-photo-wrapper">
        <a href="{{ asset('storage/' . $mainPhoto->photo_path) }}" data-lightbox="gallery" data-title="Головне фото помешкання">
            <img src="{{ asset('storage/' . $mainPhoto->photo_path) }}" class="main-photo shadow-lg rounded" alt="Головне фото помешкання">
        </a>
    </div>
@endif

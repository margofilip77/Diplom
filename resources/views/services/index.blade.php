@extends('layouts.app')

@section('content')

<style>
    :root {
        --primary-color: #4f46e5;
        --primary-hover: #4338ca;
        --secondary-color: #f3f4f6;
        --text-color: #111827;
        --border-color: #e5e7eb;
        --error-color: #ef4444;
        --success-color: #10b981;
        --dark-bg: #1f2937;
        --dark-text: #f9fafb;
    }

    .dark {
        --primary-color: #6366f1;
        --primary-hover: #4f46e5;
        --secondary-color: #1f2937;
        --text-color: #f9fafb;
        --border-color: #374151;
        --error-color: #f87171;
        --success-color: #34d399;
    }

    .hero-section {
        background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), var(--hero-bg);
        background-size: cover;
        background-position: center;
        min-height: 70vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .hero-content {
        text-align: center;
        color: white;
        max-width: 800px;
        padding: 2rem;
    }

    .cta-button {
        background-color: var(--primary-color);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        display: inline-block;
        margin: 0.5rem;
    }

    .cta-button:hover {
        background-color: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .secondary-button {
        background-color: #f59e0b;
    }

    .secondary-button:hover {
        background-color: #d97706;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .section-description {
        color: #6b7280;
        text-align: center;
        max-width: 800px;
        margin: 0 auto 2rem;
    }

    .dark .section-description {
        color: #9ca3af;
    }

    .package-card {
        background-color: white;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        height: 100%;
    }

    .dark .package-card {
        background-color: var(--dark-bg);
        border: 1px solid var(--border-color);
    }

    .package-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }

    .package-content {
        padding: 1.5rem;
    }

    .package-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }

    .package-description {
        color: #6b7280;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .dark .package-description {
        color: #9ca3af;
    }

    .service-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        color: #6b7280;
    }

    .dark .service-item {
        color: #d1d5db;
    }

    .service-icon {
        color: var(--success-color);
        margin-right: 0.5rem;
    }

    .price-container {
        margin: 1.5rem 0;
    }

    .original-price {
        text-decoration: line-through;
        color: #9ca3af;
        font-size: 0.9rem;
    }

    .discounted-price {
        color: var(--success-color);
        font-size: 1.25rem;
        font-weight: 600;
    }

    .contact-form {
        max-width: 600px;
        margin: 0 auto;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        background-color: white;
        color: var(--text-color);
    }

    .dark .form-input {
        background-color: var(--dark-bg);
        border-color: var(--border-color);
    }

    .form-textarea {
        min-height: 150px;
        resize: vertical;
    }

    .submit-button {
        background-color: var(--primary-color);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .submit-button:hover {
        background-color: var(--primary-hover);
    }

    .empty-state {
        text-align: center;
        color: #6b7280;
        padding: 2rem;
    }

    .dark .empty-state {
        color: #9ca3af;
    }

    .modal-content {
        border-radius: 15px;
    }
</style>

<section class="hero-section" style="--hero-bg: url('{{ asset('images/banner2.jpg') }}')">
    <div class="hero-content">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">Відчуйте гармонію природи</h1>
        <p class="text-xl mb-8">Насолоджуйтесь єднанням з природою. Обирайте затишне житло та послуги для вашого ідеального відпочинку.</p>
        <div class="flex flex-wrap justify-center">
            <a href="{{ route('accommodations.index') }}" class="cta-button">Знайти помешкання</a>
            <a href="#packages" class="cta-button secondary-button">Переглянути пакети</a>
        </div>
    </div>
</section>

<section class="py-16 bg-gray-50 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <h2 class="section-title">Спеціальні пропозиції та пакети послуг</h2>
        <p class="section-description">
            Спочатку оберіть помешкання, а потім додайте один із наших пакетів послуг у кошику для незабутнього відпочинку.
        </p>
    </div>
</section>

<section id="packages" class="py-16 bg-white dark:bg-gray-900">
    <div class="container mx-auto px-4">
        @if ($packages->isEmpty())
            <div class="empty-state">
                <p>На жаль, наразі немає доступних пакетів послуг.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($packages as $package)
                    <div class="package-card">
                        <div class="package-content">
                            <h3 class="package-title">{{ $package->name }}</h3>
                            <p class="package-description">{{ $package->description ?? 'Опис цього пакету поки що відсутній.' }}</p>
                            
                            <ul class="mb-4">
                                @foreach ($package->services as $service)
                                    <li class="service-item">
                                        <span class="service-icon"><i class="fas fa-check-circle"></i></span>
                                        {{ $service->name }}
                                    </li>
                                @endforeach
                            </ul>
                            
                            <div class="price-container">
                                @if ($package->discount > 0)
                                    <p class="original-price">До знижки: {{ $package->originalPrice() }} грн</p>
                                    <p class="discounted-price">Після знижки ({{ $package->discount }}%): {{ $package->calculatePrice() }} грн</p>
                                @else
                                    <p class="discounted-price">Ціна: {{ $package->calculatePrice() }} грн</p>
                                @endif
                            </div>
                            
                            <button class="cta-button inline-block w-full text-center"
                                    data-package-id="{{ $package->id }}"
                                    data-name="{{ addslashes($package->name) }}"
                                    data-description="{{ addslashes($package->description ?? 'Опис цього пакету поки що відсутній.') }}"
                                    data-price="{{ $package->calculatePrice() }}"
                                    data-discount="{{ $package->discount }}"
                                    data-original-price="{{ $package->originalPrice() }}"
                                    data-services="{{ json_encode($package->services->map(fn($service) => ['name' => $service->name])) }}"
                                    onclick="showPackageModal(this)">
                                Детальніше
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<!-- Modal for package details -->
<div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="packageModalLabel">Деталі пакета</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3 id="modal-package-name" class="mb-3"></h3>
                        <p id="modal-package-description" class="text-muted mb-3"></p>
                        <p class="mb-2"><strong>Ціна:</strong> <span id="modal-package-price"></span> грн</p>
                        <p class="mb-2 original-price" style="display: none;"><strong>До знижки:</strong> <span id="modal-package-original-price"></span> грн</p>
                        <p class="mb-2 discount" style="display: none;"><strong>Знижка:</strong> <span id="modal-package-discount"></span>%</p>
                        <p class="mb-2"><strong>Включені послуги:</strong></p>
                        <ul id="modal-package-services" class="list-unstyled" style="font-size: 0.9rem;"></ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрити</button>
            </div>
        </div>
    </div>
</div>

</section>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Плавна прокрутка до якорів
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Show package modal
    function showPackageModal(button) {
        const packageData = {
            id: button.getAttribute('data-package-id'),
            name: button.getAttribute('data-name'),
            description: button.getAttribute('data-description'),
            price: button.getAttribute('data-price'),
            originalPrice: button.getAttribute('data-original-price'),
            discount: parseFloat(button.getAttribute('data-discount')),
            services: JSON.parse(button.getAttribute('data-services'))
        };

        document.getElementById('modal-package-name').textContent = packageData.name;
        document.getElementById('modal-package-description').textContent = packageData.description;
        document.getElementById('modal-package-price').textContent = packageData.price;

        const originalPriceElement = document.querySelector('.original-price');
        const discountElement = document.querySelector('.discount');
        if (packageData.discount > 0) {
            document.getElementById('modal-package-original-price').textContent = packageData.originalPrice;
            document.getElementById('modal-package-discount').textContent = packageData.discount;
            originalPriceElement.style.display = 'block';
            discountElement.style.display = 'block';
        } else {
            originalPriceElement.style.display = 'none';
            discountElement.style.display = 'none';
        }

        const servicesList = document.getElementById('modal-package-services');
        servicesList.innerHTML = '';
        packageData.services.forEach(service => {
            const li = document.createElement('li');
            li.className = 'mb-1 d-flex align-items-center';
            li.innerHTML = `
                <span class="service-icon"><i class="fas fa-check-circle"></i></span>
                <span>${service.name}</span>
            `;
            servicesList.appendChild(li);
        });

        const modal = new bootstrap.Modal(document.getElementById('packageModal'));
        modal.show();
    }
</script>
@endsection
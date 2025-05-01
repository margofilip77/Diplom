@extends('layouts.app')

@section('content')
    @php
        $arrowImage = asset('/images/arrow.png');
    @endphp

    <style>
        .banner {
            background-image: var(--banner-bg-image);
            background-size: cover;
            background-position: center;
            position: relative;
            height: 300px;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.7));
            z-index: 1;
        }

        .banner-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: white;
            padding: 4rem 6rem;
        }

        .cta-buttons a {
            text-decoration: none;
            color: inherit;
        }

        .cta-buttons button {
            transition: all 0.3s ease-in-out;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #4CAF50;
            color: white;
            border: none;
        }

        .cta-buttons button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            background-color: #45a049;
        }

        .cta-buttons button:nth-child(3) {
            background-color: #FF9800;
        }

        .cta-buttons button:nth-child(3):hover {
            background-color: #f57c00;
        }

        .info-section {
            padding-top: 2rem;
            padding-bottom: 1rem;
            background-color: #f7f7f7;
        }

        .info-title {
            font-size: 2rem;
            md:text-3xl;
            font-weight: bold;
            color: #374151;
            margin-bottom: 1.5rem;
        }

        .info-description {
            font-size: 1rem;
            md:text-lg;
            color: #6B7280;
            margin-bottom: 2.5rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .package-card {
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .package-card:hover {
            transform: translateY(-0.5rem);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .package-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #374151;
        }

        .package-price {
            font-size: 1.25rem;
            color: #10B981;
            font-weight: 500;
        }

        .service-item {
            display: flex;
            align-items: center;
            color: #6B7280;
            margin-bottom: 0.5rem;
        }

        .service-item i {
            margin-right: 0.75rem;
            color: #48BB78;
        }

        .contact-form input[type="text"],
        .contact-form input[type="email"],
        .contact-form textarea {
            border-radius: 0.5rem;
            border: 1px solid #D1D5DB;
            padding: 0.75rem;
            width: 100%;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .contact-form button {
            border-radius: 0.5rem;
            padding: 0.75rem 2rem;
            font-weight: 500;
            transition: background-color 0.3s ease-in-out, transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
            background-color: #10B981;
            color: white;
            border: none;
        }

        .contact-form button:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            background-color: #0b815e;
        }
        .line-through {
    text-decoration: line-through;
    color: #6b7280;
}
    </style>

    <section class="banner min-h-[70vh] flex items-center justify-center" style="--banner-bg-image: url('{{ asset('images/banner2.jpg') }}');">
        <div class="overlay"></div>
        <div class="banner-content container relative z-20 text-center text-white px-6 md:px-12">
            <h1 class="text-3xl md:text-5xl font-bold mb-4 animate__animated animate__fadeInDown">Відчуйте гармонію природи</h1>
            <p class="text-lg md:text-xl text-gray-200 mb-8 animate__animated animate__fadeIn" style="animation-delay: 0.3s;">
                Насолоджуйтесь єднанням з природою. Обирайте затишне житло та послуги для вашого ідеального відпочинку.
            </p>
            <div class="cta-buttons flex flex-col sm:flex-row justify-center items-center gap-4 animate__animated animate__zoomIn" style="animation-delay: 0.6s;">
                <a href="{{ route('accommodations.index') }}">
                    <button>Знайти помешкання</button>
                </a>
                <span class="text-gray-200 font-semibold hidden sm:inline">або</span>
                <a href="#packages">
                    <button>Переглянути пакети</button>
                </a>
            </div>
        </div>
    </section>

    <section class="info-section">
        <div class="container mx-auto px-6 md:px-12 text-center">
            <h2 class="info-title animate__animated animate__fadeIn">Спеціальні пропозиції та пакети послуг</h2>
            <p class="info-description animate__animated animate__fadeIn" style="animation-delay: 0.3s;">
                Спочатку оберіть помешкання, а потім додайте один із наших пакетів послуг у кошику для незабутнього відпочинку.
            </p>
        </div>
    </section>

    <section id="packages" class="py-16 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-6 md:px-12">
        @if ($packages->isEmpty())
            <p class="text-center text-gray-600 dark:text-gray-300 text-lg">На жаль, наразі немає доступних пакетів послуг.</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($packages as $package)
                    <div class="package-card bg-white dark:bg-gray-900 shadow-lg">
                        <div class="p-6">
                            <h3 class="package-title dark:text-gray-100 mb-3">{{ $package->name }}</h3>
                            <p class="text-gray-700 dark:text-gray-300 text-sm mb-4">{{ $package->description ?? 'Опис цього пакету поки що відсутній.' }}</p>
                            <ul class="mb-4">
                                @foreach ($package->services as $service)
                                    <li class="service-item dark:text-gray-200">
                                        <i class="fas fa-check-circle"></i> {{ $service->name }}
                                    </li>
                                @endforeach
                            </ul>
                            <div class="mb-4">
                                @if ($package->discount > 0)
                                    <p class="text-gray-500 line-through">До знижки: {{ $package->originalPrice() }} грн</p>
                                    <p class="package-price dark:text-emerald-400">Після знижки ({{ $package->discount }}%): {{ $package->calculatePrice() }} грн</p>
                                @else
                                    <p class="package-price dark:text-emerald-400">Ціна: {{ $package->calculatePrice() }} грн</p>
                                @endif
                            </div>
                            <a href="{{ route('packages.show', $package->id) }}" class="inline-block bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300">
                                Детальніше
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

    <section class="py-16 bg-gray-100 dark:bg-gray-900">
        <div class="container mx-auto px-6 md:px-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white text-center mb-8">Залишились питання? Зв'яжіться з нами!</h2>
            <p class="text-md md:text-lg text-gray-600 dark:text-gray-300 mb-10 max-w-2xl mx-auto text-center">
                Наша команда з радістю відповість на всі ваші запитання та допоможе спланувати ідеальний зелений відпочинок.
            </p>
            <form action="{{ route('contact.submit') }}" method="POST" class="contact-form max-w-md mx-auto">
                @csrf
                <div>
                    <input type="text" name="name" placeholder="Ваше ім'я" required>
                </div>
                <div>
                    <input type="email" name="email" placeholder="Ваш Email" required>
                </div>
                <div>
                    <textarea name="message" rows="5" placeholder="Ваше повідомлення" required></textarea>
                </div>
                <div class="text-center">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 px-8 rounded-full shadow-md">Надіслати повідомлення</button>
                </div>
            </form>
        </div>
    </section>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
@endsection
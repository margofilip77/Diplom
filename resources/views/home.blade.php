@extends('layouts.app')

@section('content')
    <!-- Герой-секція -->
    <section class="hero-section position-relative text-white overflow-hidden">
        <img src="{{ asset('images/banner1.jpg') }}" alt="Зелений туризм" class="hero-image">
        <div class="overlay"></div>
        <div class="container position-relative py-5">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center py-5 animate__animated animate__fadeIn">
                    <h1 class="display-4 fw-bold mb-4">Відкрийте красу зеленого туризму</h1>
                    <p class="lead mb-5">Затишні помешкання, активний відпочинок і гармонія з природою чекають на вас!</p>
                    <a href="#explore" class="btn btn-success btn-lg px-5 py-3 animate__animated animate__zoomIn delay-2">Дослідити пропозиції</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Секція переваг -->
    <section class="features py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h2 class="text-center fw-bold mb-5 animate__animated animate__fadeIn">Чому обирають Зелений Шлях?</h2>
            <div class="row g-4">
                <div class="col-md-4 animate__animated animate__fadeInUp delay-0">
                    <div class="card h-100 border-0 shadow-sm text-center p-4">
                        <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                        <h3 class="card-title fw-bold">Еко-відпочинок</h3>
                        <p class="card-text text-muted">Комфортний відпочинок у гармонії з природою</p>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp delay-1">
                    <div class="card h-100 border-0 shadow-sm text-center p-4">
                        <i class="fas fa-hiking fa-3x text-success mb-3"></i>
                        <h3 class="card-title fw-bold">Активності</h3>
                        <p class="card-text text-muted">Піші прогулянки, велотури та багато іншого</p>
                    </div>
                </div>
                <div class="col-md-4 animate__animated animate__fadeInUp delay-2">
                    <div class="card h-100 border-0 shadow-sm text-center p-4">
                        <i class="fas fa-home fa-3x text-success mb-3"></i>
                        <h3 class="card-title fw-bold">Затишок</h3>
                        <p class="card-text text-muted">Комфортні еко-помешкання</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Популярні послуги -->
    <section class="popular-services py-5" style="background-color: #f8f9fa;">
        <div class="container">
            <h2 class="text-center fw-bold mb-5 animate__animated animate__fadeIn">Популярні послуги</h2>
            @if ($popularServices->isEmpty())
                <p class="text-center text-muted">Немає популярних послуг.</p>
            @else
                <div class="row g-4">
                    @foreach ($popularServices as $service)
                        <div class="col-md-4 animate__animated animate__fadeInUp delay-{{ $loop->index }}">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <img src="{{ asset('services/' . ($service->image ?? 'default-service.jpg')) }}" class="card-img-top fallback-image" alt="{{ $service->name }}" style="height: 220px; object-fit: cover;" data-image="{{ asset('services/default-service.jpg') }}">
                                <div class="card-body">
                                    <h3 class="card-title fw-bold">{{ $service->name }}</h3>
                                    <p class="card-text text-muted">{{ Str::limit($service->description ?? 'Опис відсутній', 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-success fw-bold">{{ $service->price }} грн</span>
                                    </div>
                                    <a href="{{ route('services.index') }}" class="btn btn-outline-success mt-3 w-100">Детальніше</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- Популярні помешкання (слайдер) -->
    <section id="explore" class="popular-accommodations py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5 animate__animated animate__fadeIn">Популярні помешкання</h2>
            @if ($popularAccommodations->isEmpty())
                <p class="text-center text-muted">Немає популярних помешкань.</p>
            @else
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        @foreach ($popularAccommodations as $accommodation)
                            @php
                                $mainPhoto = $accommodation->photos->first()->photo_path ?? 'images/default-accommodation.jpg';
                            @endphp
                            <div class="swiper-slide">
                                <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                    <img src="{{ asset($mainPhoto) }}" class="card-img-top" alt="{{ $accommodation->name }}" style="height: 220px; object-fit: cover;">
                                    <div class="card-body">
                                        <h3 class="card-title fw-bold">{{ $accommodation->name }}</h3>
                                        <p class="card-text text-muted">{{ Str::limit($accommodation->description ?? 'Опис відсутній', 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-success fw-bold">{{ $accommodation->price_per_night }} грн/ніч</span>
                                            <span class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= round($accommodation->average_rating) ? 'text-warning' : 'text-muted' }}"></i>
                                                @endfor
                                                ({{ number_format($accommodation->average_rating, 1) }})
                                            </span>
                                        </div>
                                        <a href="{{ route('accommodations.show', $accommodation->id) }}" class="btn btn-outline-success mt-3 w-100">Переглянути</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Навигация для слайдера -->
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-pagination"></div>
                </div>
            @endif
        </div>
    </section>

    <!-- Відгуки -->
    <section class="reviews py-5">
        <div class="container">
            <h2 class="text-center fw-bold mb-5 animate__animated animate__fadeIn">Відгуки наших гостей</h2>
            <div class="row g-4">
                @foreach([
                    ['name' => 'Анна', 'text' => 'Чудовий відпочинок у затишному будинку серед природи!', 'rating' => 5],
                    ['name' => 'Олександр', 'text' => 'Неймовірні краєвиди та активний відпочинок.', 'rating' => 4],
                    ['name' => 'Софія', 'text' => 'Все продумано для комфортного перебування.', 'rating' => 5]
                ] as $review)
                    <div class="col-md-4 animate__animated animate__fadeInUp delay-{{ $loop->index }}">
                        <div class="card h-100 border-0 shadow-sm p-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-user-circle fa-2x text-success me-2"></i>
                                <h5 class="mb-0 fw-bold">{{ $review['name'] }}</h5>
                            </div>
                            <p class="text-muted">"{{ $review['text'] }}"</p>
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $review['rating'] ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Секція заклику до дії -->
    <section class="cta-section py-5" style="background: linear-gradient(135deg, #28a745, #4CAF50); color: white;">
        <div class="container text-center">
            <h2 class="fw-bold mb-4 animate__animated animate__fadeIn">Готові до пригод?</h2>
            <p class="lead mb-4 animate__animated animate__fadeIn delay-1">Забронюйте свій ідеальний відпочинок вже сьогодні!</p>
            <a href="{{ route('accommodations.index') }}" class="btn btn-light btn-lg px-5 py-3 me-3 animate__animated animate__zoomIn delay-2">Знайти помешкання</a>

        </div>
    </section>

    <!-- Футер -->
    <footer class="footer py-5 bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold">Зелений Шлях</h5>
                    <p class="text-muted">Відпочинок у гармонії з природою. Досліджуйте красу України разом з нами!</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold">Корисні посилання</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('accommodations.index') }}" class="text-white">Помешкання</a></li>
                        <li><a href="{{ route('services.index') }}" class="text-white">Послуги</a></li>
                        <li><a href="{{ route('contact.form') }}" class="text-white">Контакти</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold">Зв’яжіться з нами</h5>
                    <p class="text-muted">Email: support@zelenyshlyakh.com</p>
                    <p class="text-muted">Телефон: +380 123 456 789</p>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-twitter fa-lg"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="text-muted">© {{ date('Y') }} Зелений Шлях. Усі права захищені.</p>
            </div>
        </div>
    </footer>

    <!-- Стилі -->
    <style>
        /* Герой-секція */
        .hero-section {
            min-height: 80vh;
            display: flex;
            align-items: center;
            position: relative;
        }

        .hero-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6));
            z-index: 2;
        }

        .hero-section .container {
            z-index: 3;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
        }

        /* Загальні стилі для карток */
        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .features .card, .reviews .card, .popular-services .card {
            background: white;
            padding: 20px;
        }

        .popular-accommodations .card {
            background: white;
        }

        /* Слайдер */
        .swiper-container {
            position: relative;
            padding-bottom: 50px;
            margin: 0 50px; /* Додаємо відступи з боків для стрілок */
        }

        .swiper-slide {
            width: 350px !important;
        }

        .swiper-button-prev, .swiper-button-next {
            color: #4CAF50;
            width: 40px;
            height: 40px;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        .swiper-button-prev:hover, .swiper-button-next:hover {
            background-color: rgba(255, 255, 255, 1);
        }

        .swiper-button-prev {
            left: -50px; /* Позиціонуємо стрілку зліва від каруселі */
        }

        .swiper-button-next {
            right: -50px; /* Позиціонуємо стрілку справа від каруселі */
        }

        .swiper-button-prev::after, .swiper-button-next::after {
            font-size: 20px; /* Розмір стрілок */
        }

        .swiper-pagination {
            bottom: 0 !important;
        }

        .swiper-pagination-bullet-active {
            background: #4CAF50;
        }

        /* Затримки анімації */
        .delay-0 { animation-delay: 0s; }
        .delay-1 { animation-delay: 0.2s; }
        .delay-2 { animation-delay: 0.4s; }
        .delay-3 { animation-delay: 0.6s; }
        .delay-4 { animation-delay: 0.8s; }
        .delay-5 { animation-delay: 1.0s; }

        /* CTA */
        .cta-section .btn {
            transition: all 0.3s ease;
        }

        .cta-section .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Футер */
        .footer a:hover {
            color: #4CAF50 !important;
            text-decoration: underline;
        }
    </style>

    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Swiper.js -->
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <script>
        // Обробник помилок для зображень
        document.addEventListener('DOMContentLoaded', function () {
            const images = document.querySelectorAll('.fallback-image');
            images.forEach(image => {
                image.addEventListener('error', function () {
                    const fallbackImage = this.getAttribute('data-image');
                    if (fallbackImage) {
                        this.src = fallbackImage;
                    }
                });
            });
        });

        // Ініціалізація Swiper
        document.addEventListener('DOMContentLoaded', function () {
            const swiper = new Swiper('.swiper-container', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    },
                },
            });
        });

        // Паралакс ефект
        window.addEventListener('scroll', () => {
            const hero = document.querySelector('.hero-image');
            const scrollPosition = window.scrollY;
            hero.style.transform = `translateY(${scrollPosition * 0.2}px)`;
        });

        // Плавний скрол
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
@endsection
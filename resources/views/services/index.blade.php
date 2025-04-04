@extends('layouts.app')

@section('content')
@php
$bgImage = asset('/images/banner.jpg'); // Замініть на своє фото
$arrowImage = asset('/images/arrow.png'); // Замініть на свою картинку стрілки
@endphp

<!-- Банер -->
<div class="banner" style="background-image: url('{{ $bgImage }}');">
    <div class="overlay"></div> <!-- Затемнення -->
    <div class="content">
        <h1>Відкрий зелений туризм</h1>
        <p>Виберіть комфортне помешкання, насолоджуйтесь природою та додайте супровідні послуги для незабутнього відпочинку</p>

        <!-- Стрілка до кнопки -->
        <div class="button-container">
            <div class="arrow-container">
                <img src="{{ $arrowImage }}" alt="Arrow" class="arrow-image"> <!-- Стрілка ззовні кнопки -->
            </div>
            <a href="{{ route('accommodations.accommodation') }}" class="btn-accommodation">
                Обрати помешкання
            </a>
            <span class="or-text">або</span> <!-- Текст "або" між кнопками -->
            <a href="{{ url('/') }}" class="btn-package">Вибрати пакет</a>
        </div>
    </div>
</div>
<!-- Контактна форма -->
<div class="contact-form">
    <h2>Зв'яжіться з нами</h2>
    <p>Маєте запитання? Напишіть нам!</p>
    <form action="{{ route('contact.submit') }}" method="POST">
        @csrf
        <input type="text" name="name" placeholder="Ваше ім'я" required>
        <input type="email" name="email" placeholder="Ваш Email" required>
        <textarea name="message" placeholder="Ваше повідомлення" rows="4" required></textarea>
        <button type="submit">Надіслати</button>
    </form>
</div>

@endsection

<style>
    /* Стиль банера */
    .banner {
        width: 83vw;
        /* Вся ширина екрану */
        height: 40vh;
        /* Зроблено довшим */
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    /* Затемнюючий шар */
    .overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6));
        /* Рівномірне затемнення */
    }

    /* Контент банера */
    .content {
        position: relative;
        z-index: 10;
        color: white;
        max-width: 600px;
    }

    .content h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .content p {
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    /* Контейнер кнопок */
    .button-container {
        display: flex;
        justify-content: center;
        gap: 20px;
        align-items: center;
        /* Вирівнюємо по вертикалі */
        margin-left: -30px;
        /* Зсуваємо контейнер вліво */
    }

    /* Текст "або" між кнопками */
    .or-text {
        font-size: 1.2rem;
        font-weight: 500;
        color: white;
    }

    /* Стиль картинки стрілки */
    .arrow-image {
        width: 50px;
        /* Розмір стрілки */
        margin-bottom: 20px;
    }

    /* Загальний стиль кнопок */
    .btn-accommodation,
    .btn-package {
        display: inline-block;
        padding: 12px 28px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        border-radius: 50px;
        transition: all 0.3s ease-in-out;
        color: white !important;
        /* Фіксуємо білий колір тексту */
    }

    /* Кнопка "Обрати помешкання" */
    .btn-accommodation {
        background-color: #4CAF50;
        /* Зелений */
        box-shadow: 0 4px 6px rgba(0, 128, 0, 0.3);
    }

    .btn-accommodation:hover {
        background-color: #3e8e41;
        /* Темніший зелений */
        box-shadow: 0 0 15px rgba(62, 142, 65, 0.7);
        /* Світіння в зелених тонах */
        transform: translateY(-3px) scale(1.05);
        /* Піднімається і трохи збільшується */
    }

    /* Кнопка "Вибрати пакет" */
    .btn-package {
        background-color: #FF9800;
        /* Помаранчевий */
        box-shadow: 0 4px 6px rgba(255, 152, 0, 0.3);
    }

    .btn-package:hover {
        background-color: #e68900;
        /* Темніший помаранчевий */
        box-shadow: 0 0 15px rgba(255, 152, 0, 0.7);
        /* Світіння в помаранчевих тонах */
        transform: translateY(-3px) scale(1.05);
        /* Піднімається і трохи збільшується */
    }

    /* Виправлення зміни кольору тексту при наведенні */
    .btn-accommodation:hover,
    .btn-package:hover {
        color: white !important;
        text-decoration: none;
    }

    .contact-form {
        background: #f8f8f8;
        padding: 40px;
        text-align: center;
        margin-top: 50px;
    }

    .contact-form h2 {
        font-size: 2rem;
        color: #333;
        margin-bottom: 10px;
    }

    .contact-form p {
        font-size: 1rem;
        color: #555;
        margin-bottom: 20px;
    }

    .contact-form form {
        max-width: 500px;
        margin: auto;
    }

    .contact-form input,
    .contact-form textarea {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 1rem;
    }

    .contact-form button {
        background: #4CAF50;
        color: white;
        border: none;
        padding: 12px 20px;
        font-size: 1rem;
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    .contact-form button:hover {
        background: #3e8e41;
        box-shadow: 0 0 10px rgba(62, 142, 65, 0.7);
    }
</style>
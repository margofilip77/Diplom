<!DOCTYPE html>
<html lang="uk">
<head>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel</title>
    
    <!-- Підключення стилів -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

    <!-- Навігаційна панель -->
    <header>
        <div class="container">
            <nav>
                <ul>
                    <li><a href="#">Головна</a></li>
                    <li><a href="#">Про нас</a></li>
                    <li><a href="#">Послуги</a></li>
                    <li><a href="#">Контакти</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Головний банер -->
    <section class="hero">
        <div class="container">
            <h1>Зелене туризму - Відпочинок серед природи</h1>
            <p>Виберіть для себе комфортний будиночок, готель чи унікальні послуги від наших партнерів.</p>
            <a href="#" class="btn">Переглянути послуги</a>
        </div>
    </section>

    <!-- Про нас -->
    <section class="about">
        <div class="container">
            <h2>Про нас</h2>
            <p>Ми пропонуємо найкращі тури та послуги для любителів природи, з екологічно чистими будиночками, готелями та незабутніми враженнями від відпочинку.</p>
        </div>
    </section>

    <!-- Контакти -->
    <section class="contact">
        <div class="container">
            <h2>Зв'яжіться з нами</h2>
            <p>Ми готові відповісти на ваші питання і допомогти обрати ідеальний тур.</p>
            <a href="mailto:info@greentourism.com" class="btn">Напишіть нам</a>
        </div>
    </section>

    <!-- Підвал -->
    <footer>
        <div class="container">
            <p>&copy; 2024 Зелене Туристичне Агентство. Усі права захищені.</p>
        </div>
    </footer>

</body>
</html>

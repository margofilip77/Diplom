<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кошик</title>
    <link rel="stylesheet" href="styles.css"> <!-- Підключаємо файл стилів -->
</head>
<body>
    <div class="container">
        <h1>Ваш Кошик</h1>

        <div id="cart-container">
            <!-- Тут буде відображатись кошик -->
        </div>

        <div id="cart-summary">
            <p><strong>Загальна кількість товарів:</strong> <span id="item-count">0</span></p>
            <p><strong>Загальна вартість:</strong> <span id="total-price">0</span> грн</p>
            <button id="checkout-btn">Оформити замовлення</button>
        </div>
    </div>

    <script src="cart.js"></script> <!-- Підключаємо скрипт для роботи з кошиком -->
</body>
</html>

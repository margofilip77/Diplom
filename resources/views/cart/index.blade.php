<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 80%;
        margin: 30px auto;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 20px;
    }

    h1 {
        font-size: 2rem;
        color: #333;
        text-align: center;
        margin-bottom: 20px;
    }

    h2 {
        font-size: 1.5rem;
        color: #ff4d4d;
        text-align: center;
    }

    .accommodation-item {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    .accommodation-item img {
        width: 200px;
        height: auto;
        border-radius: 8px;
        margin-right: 20px;
    }

    .accommodation-info {
        flex: 1;
    }

    .accommodation-info h3 {
        font-size: 1.2rem;
        color: #333;
    }

    .accommodation-info p {
        font-size: 1rem;
        color: #555;
    }

    .accommodation-info ul {
        padding-left: 20px;
    }

    .accommodation-info ul li {
        font-size: 1rem;
        color: #777;
    }

    .accommodation-info strong {
        font-size: 1.1rem;
        color: #333;
    }

    .button-group {
        margin-top: 20px;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
        margin-right: 10px;
        transition: background-color 0.3s;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn:hover {
        opacity: 0.8;
    }

    .total-section {
        margin-top: 30px;
        text-align: center;
    }

    .total-section h3 {
        font-size: 1.5rem;
        color: #333;
    }

    .total-section button {
        padding: 12px 25px;
        font-size: 1rem;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .total-section button:hover {
        background-color: #218838;
    }
</style>

<div class="container">
    <h1>Ваш кошик</h1>

    @if(!$cart || !$cart->accommodations->count())
    <h2>Кошик порожній</h2>
    @else
    @php $total = 0; @endphp

    @foreach($cart->accommodations as $accommodation)
    @php
    $guests = json_decode($accommodation->guests_count, true);

    // Розрахунок кількості ночей
    $checkinDate = \Carbon\Carbon::parse($accommodation->checkin_date);
    $checkoutDate = \Carbon\Carbon::parse($accommodation->checkout_date);
    $nights = $checkoutDate->diffInDays($checkinDate);

    // Початкова вартість за помешкання
    $accommodationTotal = $accommodation->price * $nights;

    // Оновлюємо загальну суму кошика без харчування
    $total += $accommodationTotal;
    @endphp

    <div class="accommodation-item">
        <img src="{{ asset($accommodation->accommodation_photo) }}" alt="Фото">

        <div class="accommodation-info">
            <h3>{{ $accommodation->accommodation->name }}</h3>
            <p><strong>Ціна за ніч:</strong> {{ $accommodation->price }} грн</p>
            <p><strong>Дата заїзду:</strong> {{ $accommodation->checkin_date }}</p>
            <p><strong>Дата виїзду:</strong> {{ $accommodation->checkout_date }}</p>
            <p><strong>Кількість ночей:</strong> {{ $nights }}</p>

            <p><strong>Кількість гостей:</strong></p>
            <ul>
                @if(!empty($guests['adults'])) <li>Дорослих: {{ $guests['adults'] }}</li> @endif
                @if(!empty($guests['children'])) <li>Дітей: {{ $guests['children'] }}</li> @endif
                @if(!empty($guests['infants'])) <li>Немовлят: {{ $guests['infants'] }}</li> @endif
                @if(!empty($guests['pets'])) <li>Тварин: {{ $guests['pets'] }}</li> @endif
            </ul>

            <p><strong>Підсумок за проживання: {{ $accommodationTotal }} грн</strong></p>

            <div class="button-group">
                <a href="{{ route('accommodations.show', $accommodation->accommodation->id) }}" class="btn btn-primary">Переглянути деталі</a>
                <a href="{{ route('cart.remove', $accommodation->id) }}" class="btn btn-danger" onclick="return confirm('Ви впевнені, що хочете видалити цей елемент з кошика?')">Видалити</a>
            </div>
        </div>
    </div>
    @endforeach

    <div class="total-section">
        <h3>Загальна сума: <span id="total-price">{{ $total }}</span> грн</h3>
        <button class="add-to-cart-btn" data-total="{{ $total }}">Оформити замовлення</button>
    </div>
    @endif
</div>

<script>
    document.querySelector(".add-to-cart-btn")?.addEventListener("click", function() {
        let total = this.getAttribute("data-total");
        console.log("Загальна сума: " + total);

        // Тут можна реалізувати відправку форми або redirect
    });
</script>
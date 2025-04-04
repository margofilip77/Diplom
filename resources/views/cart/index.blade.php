@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Кошик</h2>

    @if(session('cart') && count(session('cart')) > 0)
    <table class="table">
        <thead>
            <tr>
                <th>Фото</th>
                <th>Назва</th>
                <th>Ціна за ніч</th>
                <th>Дії</th>
            </tr>
        </thead>
        <tbody>
            @foreach(session('cart') as $item)
            <tr>
                <td>
                    @if($item['image'])
                    <a href="#" class="open-image" data-src="{{ asset($item['image']) }}">
                        <img src="{{ asset($item['image']) }}" width="100" alt="Фото">
                    </a>
                    @else
                    <span>Фото відсутнє</span>
                    @endif
                </td>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['price_per_night'] }} грн</td>
                <td>
                    <a href="{{ route('accommodations.show', $item['id']) }}" class="btn btn-primary">Переглянути деталі</a>
                    <a href="{{ route('cart.remove', $item['id']) }}" class="btn btn-danger">Видалити</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>Кошик порожній.</p>
    @endif
</div>

<!-- Модальне вікно для збільшення фото -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="Фото">
            </div>
        </div>
    </div>
</div>
<div id="cart-items"></div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const cartContainer = document.getElementById("cart-items");
        let cart = JSON.parse(localStorage.getItem("cart")) || [];

        if (cart.length === 0) {
            cartContainer.innerHTML = "<p>Ваш кошик порожній</p>";
            return;
        }

        cart.forEach((item, index) => {
            let mealList = (item.meals && item.meals.length > 0) ?
                item.meals.map(meal => `${meal.name}: ${meal.price} грн за гостя × ${meal.count || 0}`).join("<br>") :
                "Без харчування";

            let guestSummary = `Дорослі: ${item.guests.adults}, Діти: ${item.guests.children}, Немовлята: ${item.guests.infants}, Тварини: ${item.guests.pets}`;

            let cartItemHtml = `
            <div class="cart-item border p-3 mb-2">
                <p><strong>Житло:</strong> №${item.id}</p>
                <p><strong>Ціна:</strong> ${item.price} грн/ніч</p>
                <p><strong>Гості:</strong> ${guestSummary}</p>
                <p><strong>Харчування:</strong><br> ${mealList}</p>
                <button class="btn btn-danger btn-sm remove-btn" data-index="${index}">Видалити</button>
            </div>
        `;
            cartContainer.innerHTML += cartItemHtml;
        });

        // Додаємо обробник подій для кнопок видалення
        document.querySelectorAll(".remove-btn").forEach(button => {
            button.addEventListener("click", function() {
                let index = this.getAttribute("data-index");
                removeFromCart(index);
            });
        });
    });

    // Функція видалення з кошика без перезавантаження сторінки
    function removeFromCart(index) {
        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        cart.splice(index, 1);
        localStorage.setItem("cart", JSON.stringify(cart));

        // Видаляємо елемент з DOM без перезавантаження
        document.getElementById("cart-items").innerHTML = "";
        document.dispatchEvent(new Event("DOMContentLoaded"));
    }
</script>

@endsection

@section('scripts')
<script>
    // Збільшення фото
    document.querySelectorAll('.open-image').forEach(function(element) {
        element.addEventListener('click', function(e) {
            e.preventDefault();
            var imgSrc = element.getAttribute('data-src');
            document.getElementById('modalImage').src = imgSrc;
            $('#imageModal').modal('show'); // Відкриває модальне вікно
        });
    });
</script>
@endsection
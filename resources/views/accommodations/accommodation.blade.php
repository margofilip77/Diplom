@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">🔍 Знайдіть ідеальне помешкання</h2>

    {{-- Форма пошуку --}}
    <div class="card p-4 shadow">
        <form id="searchForm">
            <div class="row align-items-end">
                {{-- Місце розташування --}}
                <div class="col-md-3">
                    <label for="location">📍 Місце розташування</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="Введіть місто">
                </div>

                {{-- Дата заїзду --}}
                <div class="col-md-2">
                    <label for="check_in">📅 Дата заїзду</label>
                    <input type="text" class="form-control datepicker" id="check_in" name="check_in">
                </div>

                {{-- Дата виїзду --}}
                <div class="col-md-2">
                    <label for="check_out">📅 Дата виїзду</label>
                    <input type="text" class="form-control datepicker" id="check_out" name="check_out">
                </div>

                {{-- Кількість осіб --}}
                <div class="col-md-2">
                    <label for="guests">👤 Кількість осіб</label>
                    <input type="number" class="form-control" id="guests" name="guests" min="1" value="1">
                </div>

                {{-- Кнопка пошуку --}}
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">🔍 Пошук</button>
                </div>
            </div>
        </form>
    </div>

    {{-- Контейнер для результатів пошуку --}}
    <div class="mt-4" id="searchResults"></div>
</div>

{{-- Підключаємо Flatpickr --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

{{-- Підключаємо AJAX --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ініціалізація Flatpickr
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            minDate: "today"
        });

        // AJAX-пошук
        document.getElementById("searchForm").addEventListener("submit", function(event) {
            event.preventDefault(); // Відміна перезавантаження

            let formData = new FormData(this);
            let url = "{{ route('accommodations.search') }}";

            fetch(url, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById("searchResults").innerHTML = html;
            })
            .catch(error => console.error("Помилка пошуку:", error));
        });
    });
</script>
@endsection

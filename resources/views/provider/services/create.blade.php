@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Додати нову послугу</h1>

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('provider.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-group mb-4">
            <label for="name" class="form-label">Назва послуги</label>
            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="description" class="form-label">Опис</label>
            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
            @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="price" class="form-label">Ціна (грн)</label>
            <input type="number" name="price" id="price" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
            @error('price')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="duration" class="form-label">Тривалість (хвилини)</label>
            <input type="number" name="duration" id="duration" class="form-control @error('duration') is-invalid @enderror" value="{{ old('duration') }}" required>
            @error('duration')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="category_id" class="form-label">Категорія</label>
            <select name="category_id" id="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
                <option value="">Оберіть категорію</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-4">
            <label for="photos" class="form-label">Фотографії (опціонально)</label>
            <input type="file" name="photos[]" id="photos" class="form-control-file @error('photos.*') is-invalid @enderror" multiple>
            <small class="form-text text-muted">Можна завантажити кілька фотографій одночасно. Утримуйте Ctrl (або Cmd на Mac), щоб обрати кілька файлів.</small>
            @error('photos.*')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary btn-lg">Додати послугу</button>
    </form>
</div>

<style>
    .form-control, .form-select {
        border-radius: 6px;
        box-shadow: none;
    }
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .btn-primary {
        border-radius: 6px;
        padding: 10px 20px;
    }
</style>

<script>
// Забороняємо введення знака "-" у поле для ціни
function preventNegativeInput(input) {
    input.addEventListener('keydown', function(event) {
        if (event.key === '-') {
            event.preventDefault();
        }
    });
    input.addEventListener('input', function() {
        if (this.value < 0) {
            this.value = 0;
        }
    });
}

// Застосовуємо до поля price
const priceInput = document.getElementById('price');
preventNegativeInput(priceInput);
</script>
@endsection
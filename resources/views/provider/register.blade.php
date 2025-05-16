@extends('layouts.app')

@section('title', 'Реєстрація надавача')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="text-center mb-4">Реєстрація як надавач</h2>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
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

                    <p>Заповніть форму, щоб зареєструватися як надавач і почати додавати свої помешкання чи послуги на платформу "Зелений Шлях".</p>

                    <form method="POST" action="{{ route('provider.register') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Ім’я</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Електронна пошта</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Підтвердження пароля</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Телефон (опціонально)</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Зареєструватися як надавач</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
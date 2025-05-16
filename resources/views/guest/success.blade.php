@extends('layouts.app')

@section('content')
<div class="container my-5 text-center">
    <h2 class="fw-bold mb-4" style="color: #1A1A1A;">Вітаємо!</h2>
    <p class="lead mb-4" style="color: #6B7280;">Ваше замовлення успішно оформлено та оплачено!</p>
    <p class="mb-4" style="color: #6B7280;">Щоб переглядати свої бронювання та керувати ними, зареєструйтеся або увійдіть до свого акаунта. Ми автоматично перенесемо ваші дані після реєстрації.</p>

    <div class="d-flex justify-content-center gap-3">
        <a href="{{ route('login') }}" class="btn" style="background: #1A73E8; color: #FFFFFF; border-radius: 25px; padding: 10px 20px;">Увійти</a>
        <a href="{{ route('register') }}" class="btn" style="background: #28a745; color: #FFFFFF; border-radius: 25px; padding: 10px 20px;">Зареєструватися</a>
    </div>

    @if(session('success'))
    <div class="alert alert-success mt-4" role="alert">
        {{ session('success') }}
    </div>
    @endif
</div>
@endsection
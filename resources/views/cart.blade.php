@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Мій кошик</h1>

        @if(session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif

        @if($cart && count($cart) > 0)
            <div class="list-group">
                @foreach($cart as $accommodationId => $item)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <span>{{ $item['name'] }} ({{ $item['quantity'] }} шт.)</span>
                            <span>{{ $item['total_price'] }} грн</span>
                        </div>
                        <form action="{{ route('cart.remove', $accommodationId) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Видалити</button>
                        </form>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-between">
                <h4>Загальна вартість: {{ array_sum(array_column($cart, 'total_price')) }} грн</h4>
                <a href="{{ route('checkout.index') }}" class="btn btn-success">Перейти до оформлення</a>
            </div>
        @else
            <p>Ваш кошик порожній.</p>
        @endif
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <h1>Привіт, {{ $user->name }}!</h1>
    <p>Ласкаво просимо на вашу домашню сторінку.</p>
    <a href="{{ route('logout') }}">Вийти</a>
@endsection

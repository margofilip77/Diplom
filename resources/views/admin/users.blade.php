@extends('layouts.app')

@section('title', 'Користувачі - Адмін-панель')

@section('content')
<div class="container mt-4">
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
            <a href="{{ route('home') }}" class="btn btn-primary mt-2">Повернутися на головну</a>
        </div>
    @else
        <h1>Користувачі</h1>
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        @if (session('role_status'))
            <div class="alert alert-success">
                {{ session('role_status') }}
            </div>
        @endif
        @if (session('role_error'))
            <div class="alert alert-danger">
                {{ session('role_error') }}
            </div>
        @endif
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ім'я</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                    <option value="client" {{ $user->role === 'client' ? 'selected' : '' }}>Клієнт</option>
                                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Адміністратор</option>
                                    <option value="provider" {{ $user->role === 'provider' ? 'selected' : '' }}>Надавач</option>
                                </select>
                            </form>
                        </td>
                        <td>{{ $user->is_blocked ? 'Заблоковано' : 'Активний' }}</td>
                        <td>
                            <form action="{{ route('admin.users.toggle-block', $user) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $user->is_blocked ? 'btn-success' : 'btn-danger' }}">
                                    {{ $user->is_blocked ? 'Розблокувати' : 'Заблокувати' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
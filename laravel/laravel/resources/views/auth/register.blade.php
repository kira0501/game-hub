@extends('layout')

@section('content')
<div class="auth-card">
    <h1>Регистрация</h1>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="fullname">ФИО</label>
        <input id="fullname" name="fullname" value="{{ old('fullname') }}" required>
        @error('fullname') <div class="error">{{ $message }}</div> @enderror

        <label for="login">Логин</label>
        <input id="login" name="login" value="{{ old('login') }}" required>
        @error('login') <div class="error">{{ $message }}</div> @enderror

        <label for="phone">Телефон</label>
        <input id="phone" name="phone" value="{{ old('phone') }}" required>
        @error('phone') <div class="error">{{ $message }}</div> @enderror

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required>
        @error('email') <div class="error">{{ $message }}</div> @enderror

        <label for="password">Пароль</label>
        <input id="password" name="password" type="password" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror

        <label for="password_confirmation">Повторите пароль</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required>

        <button type="submit">Создать аккаунт</button>
    </form>
</div>
@endsection

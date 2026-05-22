@extends('layout')

@section('content')
<div class="auth-card">
    <h1>Вход</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">Пароль</label>
        <input id="password" name="password" type="password" required>
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label class="checkbox">
            <input type="checkbox" name="remember">
            Запомнить меня
        </label>

        <button type="submit">Войти</button>
    </form>

    <p><a href="{{ route('password.request') }}">Забыли пароль?</a></p>
</div>
@endsection

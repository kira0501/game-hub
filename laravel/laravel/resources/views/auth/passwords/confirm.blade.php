@extends('layout')

@section('content')
<div class="auth-card">
    <h1>Подтверждение пароля</h1>
    <p>Для продолжения подтвердите пароль от аккаунта.</p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <label for="password">Пароль</label>
        <input id="password" type="password" name="password" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror

        <button type="submit">Подтвердить</button>
    </form>
</div>
@endsection

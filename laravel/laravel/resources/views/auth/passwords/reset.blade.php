@extends('layout')

@section('content')
<div class="auth-card">
    <h1>Новый пароль</h1>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
        @error('email') <div class="error">{{ $message }}</div> @enderror

        <label for="password">Новый пароль</label>
        <input id="password" type="password" name="password" required>
        @error('password') <div class="error">{{ $message }}</div> @enderror

        <label for="password_confirmation">Повторите пароль</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required>

        <button type="submit">Сохранить пароль</button>
    </form>
</div>
@endsection

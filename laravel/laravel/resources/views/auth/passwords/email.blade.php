@extends('layout')

@section('content')
<div class="auth-card">
    <h1>Восстановление пароля</h1>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        @error('email')
            <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit">Отправить ссылку</button>
    </form>
</div>
@endsection

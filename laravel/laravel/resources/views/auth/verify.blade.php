@extends('layout')

@section('content')
<div class="auth-card">
    <h1>Подтверждение email</h1>

    @if (session('resent'))
        <div class="alert">Новая ссылка подтверждения отправлена на ваш email.</div>
    @endif

    <p>Перед продолжением проверьте почту и перейдите по ссылке подтверждения.</p>

    <form method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit">Отправить ссылку повторно</button>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<section class="hub-container flex min-h-[70vh] items-center justify-center py-10">
    <form method="POST" action="{{ route('login') }}" class="hub-panel w-full max-w-md space-y-4 p-6">@csrf
        <h1 class="text-2xl font-black text-white">Вход</h1>
        <input name="email" type="email" class="hub-input" placeholder="Email" required>
        <div class="relative">
            <input id="login-password" name="password" type="password" class="hub-input pr-28" placeholder="Пароль" required>
            <button type="button" class="password-toggle absolute right-2 top-1/2 -translate-y-1/2 text-sm text-cyan-300" data-target="login-password">Показать</button>
        </div>
        <label class="flex items-center gap-2 text-sm text-slate-300"><input type="checkbox" name="remember" class="rounded bg-black/30"> Запомнить меня</label>
        @if($errors->any())<div class="text-sm text-red-300">{{ $errors->first() }}</div>@endif
        <button class="hub-btn w-full">Войти</button>
        <p class="text-sm text-slate-400">Нет аккаунта? <a class="text-cyan-300" href="{{ route('register') }}">Зарегистрироваться</a></p>
    </form>
</section>
@push('scripts')
<script>
    document.addEventListener('click', (event) => {
        const button = event.target.closest('.password-toggle');
        if (!button) return;
        const input = document.getElementById(button.dataset.target);
        input.type = input.type === 'password' ? 'text' : 'password';
        button.textContent = input.type === 'password' ? 'Показать' : 'Скрыть';
    });
</script>
@endpush
@endsection

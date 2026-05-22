@extends('layouts.app')

@section('content')
<section class="hub-container flex min-h-[70vh] items-center justify-center py-10">
    <form method="POST" action="{{ route('register') }}" class="hub-panel w-full max-w-md space-y-4 p-6">@csrf
        <h1 class="text-2xl font-black text-white">Регистрация</h1>
        <input name="name" class="hub-input" placeholder="Имя" required>
        <input name="email" type="email" class="hub-input" placeholder="Email" required>
        <div class="relative">
            <input id="register-password" name="password" type="password" class="hub-input pr-28" placeholder="Пароль" required>
            <button type="button" class="password-toggle absolute right-2 top-1/2 -translate-y-1/2 text-sm text-cyan-300" data-target="register-password">Показать</button>
        </div>
        <div class="relative">
            <input id="register-password-confirmation" name="password_confirmation" type="password" class="hub-input pr-28" placeholder="Повторите пароль" required>
            <button type="button" class="password-toggle absolute right-2 top-1/2 -translate-y-1/2 text-sm text-cyan-300" data-target="register-password-confirmation">Показать</button>
        </div>
        @if($errors->any())<div class="text-sm text-red-300">{{ $errors->first() }}</div>@endif
        <button class="hub-btn w-full">Создать аккаунт</button>
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

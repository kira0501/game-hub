<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Game Hub' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-hub-bg">
    @php
        $navClass = fn (bool $active) => $active
            ? 'border-b-2 border-cyan-300 pb-1 text-cyan-300'
            : 'border-b-2 border-transparent pb-1 text-slate-300 hover:text-cyan-300';
        $mobileNavClass = fn (bool $active) => $active
            ? 'rounded-md border border-cyan-300/30 bg-cyan-400/10 px-3 py-2 text-cyan-200'
            : 'rounded-md border border-white/10 bg-white/5 px-3 py-2 text-slate-200 hover:border-cyan-300/40 hover:text-cyan-200';
    @endphp
    <header class="sticky top-0 z-40 border-b border-white/10 bg-hub-bg/90 backdrop-blur">
        <div class="hub-container flex min-h-16 items-center gap-3 md:gap-4">
            <a href="{{ route('home') }}" class="text-xl font-black tracking-wide text-cyan-300">Game Hub</a>
            <nav class="hidden items-center gap-4 text-sm text-slate-300 md:flex">
                <a class="{{ $navClass(request()->routeIs('games.*') || request()->routeIs('genres.*')) }}" href="{{ route('games.index') }}">Каталог</a>
                <a class="{{ $navClass(request()->routeIs('recommendations')) }}" href="{{ route('recommendations') }}">Рекомендации</a>
                @auth
                    <a class="{{ $navClass(request()->routeIs('pc.*')) }}" href="{{ route('pc.index') }}">Проверка ПК</a>
                    <a class="{{ $navClass(request()->routeIs('favorites.*')) }}" href="{{ route('favorites.index') }}">Избранное</a>
                    @if(auth()->user()->isAdmin())
                        <a class="{{ $navClass(request()->routeIs('admin.*')) }}" href="{{ route('admin.dashboard') }}">Админка</a>
                    @endif
                @endauth
            </nav>
            <form action="{{ route('games.index') }}" class="ml-auto hidden max-w-md flex-1 md:block">
                <input name="q" value="{{ request('q') }}" class="hub-input h-10" placeholder="Поиск игр, жанров, студий">
            </form>
            <div class="ml-auto hidden items-center gap-2 md:flex">
                @auth
                    <a href="{{ route('profile.edit') }}" class="hub-btn-secondary">{{ auth()->user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="hub-btn-secondary">Выйти</button></form>
                @else
                    <a href="{{ route('login') }}" class="hub-btn-secondary">Войти</a>
                    <a href="{{ route('register') }}" class="hub-btn">Регистрация</a>
                @endauth
            </div>
            <button type="button" id="mobile-menu-toggle" class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-md border border-white/10 bg-white/5 text-2xl leading-none text-slate-100 md:hidden" aria-label="Открыть меню" aria-expanded="false">☰</button>
        </div>
        <div id="mobile-menu" class="hidden border-t border-white/10 bg-hub-bg/95 md:hidden">
            <div class="hub-container grid gap-3 py-4">
                <form action="{{ route('games.index') }}">
                    <input name="q" value="{{ request('q') }}" class="hub-input h-11" placeholder="Поиск игр, жанров, студий">
                </form>
                <nav class="grid gap-2 text-sm font-semibold">
                    <a class="{{ $mobileNavClass(request()->routeIs('games.*') || request()->routeIs('genres.*')) }}" href="{{ route('games.index') }}">Каталог</a>
                    <a class="{{ $mobileNavClass(request()->routeIs('recommendations')) }}" href="{{ route('recommendations') }}">Рекомендации</a>
                    @auth
                        <a class="{{ $mobileNavClass(request()->routeIs('pc.*')) }}" href="{{ route('pc.index') }}">Проверка ПК</a>
                        <a class="{{ $mobileNavClass(request()->routeIs('favorites.*')) }}" href="{{ route('favorites.index') }}">Избранное</a>
                        @if(auth()->user()->isAdmin())
                            <a class="{{ $mobileNavClass(request()->routeIs('admin.*')) }}" href="{{ route('admin.dashboard') }}">Админка</a>
                        @endif
                    @endauth
                </nav>
                <div class="grid grid-cols-2 gap-2">
                    @auth
                        <a href="{{ route('profile.edit') }}" class="hub-btn-secondary min-w-0 truncate">{{ auth()->user()->name }}</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button class="hub-btn-secondary w-full">Выйти</button></form>
                    @else
                        <a href="{{ route('login') }}" class="hub-btn-secondary">Войти</a>
                        <a href="{{ route('register') }}" class="hub-btn">Регистрация</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @if(session('status'))
        <div class="hub-container mt-4">
            <div class="rounded-md border border-cyan-400/30 bg-cyan-400/10 px-4 py-3 text-cyan-100">{{ session('status') }}</div>
        </div>
    @endif

    <main>
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <footer class="border-t border-white/10 bg-black/30">
        <div class="hub-container grid gap-8 py-10 md:grid-cols-[1.2fr_.8fr_.8fr]">
            <div>
                <a href="{{ route('home') }}" class="text-2xl font-black text-cyan-300">Game Hub</a>
                <p class="mt-3 max-w-xl text-sm leading-6 text-slate-400">
                    Дипломный игровой каталог с рекомендациями, отзывами, проверкой ПК и сравнением цен. Проект сделан как полноценная основа для защиты и дальнейшей разработки.
                </p>
            </div>
            <div>
                <h3 class="font-bold text-white">Навигация</h3>
                <div class="mt-3 grid gap-2 text-sm text-slate-400">
                    <a class="hover:text-cyan-300" href="{{ route('games.index') }}">Каталог игр</a>
                    <a class="hover:text-cyan-300" href="{{ route('prices.compare') }}">Скидки и цены</a>
                    <a class="hover:text-cyan-300" href="{{ route('recommendations') }}">Рекомендации</a>
                    @auth<a class="hover:text-cyan-300" href="{{ route('pc.index') }}">Проверка ПК</a>@endauth
                </div>
            </div>
            <div>
                <h3 class="font-bold text-white">Контакты автора</h3>
                <div class="mt-3 flex flex-wrap gap-3">
                    <a href="https://t.me/KirillRagozin1" target="_blank" rel="noopener noreferrer" class="hub-btn-secondary">Telegram</a>
                    <a href="https://www.instagram.com/i_kr67?igsh=dnVhcW0ybzQ1M25i&utm_source=qr" target="_blank" rel="noopener noreferrer" class="hub-btn-secondary">Instagram</a>
                </div>
                <p class="mt-4 text-xs text-slate-500">Связаться с автором проекта можно через Telegram или Instagram.</p>
            </div>
        </div>
        <div class="border-t border-white/10 py-4">
            <div class="hub-container flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500">
                <span>Game Hub, {{ date('Y') }}</span>
                <span>Laravel 11 · Blade · Tailwind CSS · MySQL</span>
            </div>
        </div>
    </footer>
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggle = document.getElementById('mobile-menu-toggle');
            const menu = document.getElementById('mobile-menu');
            if (!toggle || !menu) return;

            toggle.addEventListener('click', () => {
                const isOpen = !menu.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', String(isOpen));
                toggle.textContent = isOpen ? '×' : '☰';
            });
        });
    </script>
</body>
</html>

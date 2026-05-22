<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Game Hub' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="grid min-h-screen lg:grid-cols-[260px_1fr]">
        <aside class="border-r border-white/10 bg-slate-900/80 p-5">
            <a href="{{ route('admin.dashboard') }}" class="text-2xl font-black text-cyan-300">Game Hub Admin</a>
            <nav class="mt-8 grid gap-2 text-sm">
                <a class="rounded-md px-3 py-2 hover:bg-white/10" href="{{ route('admin.dashboard') }}">Dashboard</a>
                <a class="rounded-md px-3 py-2 hover:bg-white/10" href="{{ route('admin.games.index') }}">Игры</a>
                <a class="rounded-md px-3 py-2 hover:bg-white/10" href="{{ route('admin.genres.index') }}">Жанры</a>
                <a class="rounded-md px-3 py-2 hover:bg-white/10" href="{{ route('admin.reviews.index') }}">Отзывы</a>
                <a class="rounded-md px-3 py-2 hover:bg-white/10" href="{{ route('admin.users.index') }}">Пользователи</a>
                <a class="rounded-md px-3 py-2 hover:bg-white/10" href="{{ route('admin.stores.index') }}">Магазины</a>
                <a class="rounded-md px-3 py-2 hover:bg-white/10" href="{{ route('admin.prices.index') }}">Цены</a>
                <a class="mt-6 rounded-md px-3 py-2 text-cyan-300 hover:bg-white/10" href="{{ route('home') }}">На сайт</a>
            </nav>
        </aside>
        <main class="p-5 lg:p-8">
            @if(session('status'))
                <div class="mb-5 rounded-md border border-cyan-400/30 bg-cyan-400/10 px-4 py-3 text-cyan-100">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>

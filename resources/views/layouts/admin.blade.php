<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Game Hub' }}</title>
    @php
        $assetBase = rtrim(request()->getBaseUrl(), '/');
        if ($assetBase === '') {
            $scriptName = str_replace('\\', '/', (string) request()->server('SCRIPT_NAME'));
            $scriptBase = rtrim(str_replace('/index.php', '', $scriptName), '/');
            $assetBase = $scriptBase === '' ? '' : $scriptBase;
        }
        $viteManifest = collect([
            public_path('build/manifest.json'),
            base_path('public_html/build/manifest.json'),
        ])->first(fn ($path) => is_file($path));
        $viteAssets = $viteManifest
            ? json_decode((string) file_get_contents($viteManifest), true)
            : null;
        $viteBuildPath = $viteManifest ? dirname($viteManifest) : null;
        $viteBase = rtrim(request()->getBaseUrl(), '/');
        if ($viteBase === '') {
            $scriptName = str_replace('\\', '/', (string) request()->server('SCRIPT_NAME'));
            $scriptBase = rtrim(str_replace('/index.php', '', $scriptName), '/');
            $viteBase = $scriptBase === '' ? '' : $scriptBase;
        }
    @endphp
    @if($viteAssets)
        @isset($viteAssets['resources/css/app.css']['file'])
            <link rel="stylesheet" href="{{ $viteBase }}/build/{{ $viteAssets['resources/css/app.css']['file'] }}">
            @php
                $criticalCssPath = $viteBuildPath.DIRECTORY_SEPARATOR.$viteAssets['resources/css/app.css']['file'];
            @endphp
            @if(is_file($criticalCssPath))
                <style>{!! file_get_contents($criticalCssPath) !!}</style>
            @endif
        @endisset
        @isset($viteAssets['resources/js/app.js']['file'])
            <script type="module" src="{{ $viteBase }}/build/{{ $viteAssets['resources/js/app.js']['file'] }}"></script>
        @endisset
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <link rel="icon" type="image/svg+xml" href="{{ $assetBase }}/favicon.svg">
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

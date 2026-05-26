<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Game Hub' }}</title>
    @php
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
</head>
<body class="flex min-h-screen flex-col bg-hub-bg">
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
            <form action="{{ route('games.index') }}" class="steam-search relative ml-auto hidden max-w-md flex-1 md:block" data-search-box>
                <div class="flex">
                    <input name="q" value="{{ request('q') }}" autocomplete="off" class="hub-input h-10 rounded-r-none border-r-0" placeholder="Поиск игр, жанров, студий" data-search-input>
                    <button class="grid h-10 w-12 place-items-center rounded-r-md border border-cyan-300/40 bg-cyan-400 text-slate-950 transition hover:bg-cyan-300" aria-label="Найти">
                        <span class="text-lg">⌕</span>
                    </button>
                </div>
                <div class="steam-search-results hidden" data-search-results></div>
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
                <form action="{{ route('games.index') }}" class="steam-search relative" data-search-box>
                    <div class="flex">
                        <input name="q" value="{{ request('q') }}" autocomplete="off" class="hub-input h-11 rounded-r-none border-r-0" placeholder="Поиск игр, жанров, студий" data-search-input>
                        <button class="grid h-11 w-12 place-items-center rounded-r-md border border-cyan-300/40 bg-cyan-400 text-slate-950" aria-label="Найти">⌕</button>
                    </div>
                    <div class="steam-search-results hidden" data-search-results></div>
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

    <main class="flex-1">
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
                    <a href="https://vk.com/k.ragozin2k19" target="_blank" rel="noopener noreferrer" class="hub-btn-secondary group">
                        <svg class="h-5 w-5 text-cyan-200 transition group-hover:text-white" viewBox="0 0 24 24" aria-hidden="true" fill="currentColor">
                            <path d="M13.1 18.5C5.5 18.5 1.2 13.3 1 4.7h3.8c.1 6.3 2.9 8.9 5.1 9.5V4.7h3.6v5.4c2.2-.2 4.5-2.6 5.3-5.4h3.6c-.6 3.4-3.1 5.8-4.9 6.8 1.8.8 4.7 2.8 5.8 7h-4c-.9-2.7-2.9-4.7-5.8-5v5h-.4Z"/>
                        </svg>
                        <span>ВКонтакте</span>
                    </a>
                    <a href="https://max.ru/u/f9LHodD0cOLSVMHsv_ueylIaO22BChCnjoOOSRIkshNFTpkQe6GJDmXWEWM" target="_blank" rel="noopener noreferrer" class="hub-btn-secondary group">
                        <svg class="h-5 w-5 text-cyan-200 transition group-hover:text-white" viewBox="0 0 24 24" aria-hidden="true" fill="none">
                            <path d="M5.2 17.8V6.2h2.7l4.1 5.6 4.1-5.6h2.7v11.6h-3V11l-3.1 4.1h-1.4L8.2 11v6.8h-3Z" fill="currentColor"/>
                            <path d="M4 4.5h16M4 19.5h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" opacity=".55"/>
                        </svg>
                        <span>MAX</span>
                    </a>
                </div>
                <p class="mt-4 text-xs text-slate-500">Связаться с автором проекта можно через ВКонтакте или мессенджер MAX.</p>
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
    <style>
        .steam-search-results {
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 8px);
            z-index: 80;
            overflow: hidden;
            border: 1px solid rgba(103, 232, 249, .35);
            border-radius: 8px;
            background: linear-gradient(180deg, rgba(22, 35, 51, .98), rgba(7, 16, 24, .98));
            box-shadow: 0 18px 42px rgba(0, 0, 0, .45);
        }

        .steam-search-item {
            display: grid;
            grid-template-columns: 56px minmax(0, 1fr) auto;
            gap: 12px;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, .07);
        }

        .steam-search-item:last-child {
            border-bottom: 0;
        }

        .steam-search-item:hover {
            background: rgba(34, 211, 238, .12);
        }

        .steam-search-item img {
            width: 56px;
            height: 72px;
            object-fit: cover;
            border-radius: 4px;
            background: #0f172a;
        }
    </style>
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

            const endpoint = @json(route('search.suggest'));
            const boxes = Array.from(document.querySelectorAll('[data-search-box]'));
            let searchTimer = null;

            function renderResults(container, items) {
                if (!items.length) {
                    container.innerHTML = '<div class="p-3 text-sm text-slate-400">Ничего не найдено</div>';
                    container.classList.remove('hidden');
                    return;
                }

                container.innerHTML = items.map((item) => `
                    <a class="steam-search-item" href="${item.url}">
                        <img src="${item.image}" alt="">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-black text-white">${item.title}</span>
                            <span class="mt-1 block truncate text-xs text-slate-400">${item.meta || ''}</span>
                        </span>
                        <span class="text-right text-xs font-black text-cyan-200">
                            ${item.discount ? `<span class="mb-1 block rounded bg-lime-400/15 px-1 text-lime-300">-${item.discount}%</span>` : ''}
                            ${item.price}
                        </span>
                    </a>
                `).join('');
                container.classList.remove('hidden');
            }

            boxes.forEach((box) => {
                const input = box.querySelector('[data-search-input]');
                const results = box.querySelector('[data-search-results]');
                if (!input || !results) return;

                input.addEventListener('input', () => {
                    clearTimeout(searchTimer);
                    const query = input.value.trim();
                    if (query.length < 2) {
                        results.classList.add('hidden');
                        results.innerHTML = '';
                        return;
                    }

                    searchTimer = setTimeout(async () => {
                        const response = await fetch(`${endpoint}?q=${encodeURIComponent(query)}`, {
                            headers: { 'Accept': 'application/json' },
                        });
                        renderResults(results, await response.json());
                    }, 180);
                });

                input.addEventListener('focus', () => {
                    if (results.innerHTML.trim()) results.classList.remove('hidden');
                });
            });

            document.addEventListener('click', (event) => {
                boxes.forEach((box) => {
                    if (!box.contains(event.target)) {
                        box.querySelector('[data-search-results]')?.classList.add('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>

@extends('layouts.app')

@section('content')
@php
    $avatar = $user->avatar ?: 'https://api.dicebear.com/9.x/bottts/svg?seed='.urlencode($user->email);
    $roleLabel = $user->isAdmin() ? 'Администратор' : 'Игрок';
    $statusLabel = $user->status === 'active' ? 'Активен' : 'Ограничен';
@endphp

<section class="relative overflow-hidden border-b border-white/10">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(34,211,238,.18),transparent_35%),linear-gradient(135deg,#0b1118_0%,#111a24_55%,#071018_100%)]"></div>
    <div class="hub-container relative py-10">
        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_340px] lg:items-end">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end">
                <img src="{{ $avatar }}" alt="{{ $user->name }}" class="h-28 w-28 rounded-lg border border-cyan-300/30 bg-slate-950 object-cover shadow-[0_0_28px_rgba(34,211,238,.18)]">
                <div class="min-w-0">
                    <div class="mb-3 flex flex-wrap gap-2">
                        <span class="rounded bg-cyan-400/15 px-2.5 py-1 text-xs font-bold uppercase tracking-widest text-cyan-200">{{ $roleLabel }}</span>
                        <span class="rounded bg-white/10 px-2.5 py-1 text-xs font-bold uppercase tracking-widest text-slate-200">{{ $statusLabel }}</span>
                    </div>
                    <h1 class="break-words text-4xl font-black text-white md:text-5xl">{{ $user->name }}</h1>
                    <p class="mt-2 text-slate-400">{{ $user->email }}</p>
                    <p class="mt-1 text-sm text-slate-500">В Game Hub с {{ $user->created_at?->translatedFormat('j F Y') }}</p>
                </div>
            </div>
            <div class="grid gap-3 min-[420px]:grid-cols-3">
                <div class="rounded-lg border border-white/10 bg-black/25 p-4 text-center">
                    <p class="text-2xl font-black text-cyan-300">{{ $user->favorite_games_count }}</p>
                    <p class="mt-1 text-xs text-slate-400">Избранное</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-black/25 p-4 text-center">
                    <p class="text-2xl font-black text-cyan-300">{{ $user->reviews_count }}</p>
                    <p class="mt-1 text-xs text-slate-400">Отзывы</p>
                </div>
                <div class="rounded-lg border border-white/10 bg-black/25 p-4 text-center">
                    <p class="text-2xl font-black text-cyan-300">{{ $user->pc_configs_count }}</p>
                    <p class="mt-1 text-xs text-slate-400">ПК</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="hub-container grid gap-6 py-8 lg:grid-cols-[minmax(0,1fr)_360px]">
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <a href="{{ route('favorites.index') }}" class="rounded-lg border border-white/10 bg-white/5 p-5 transition hover:-translate-y-1 hover:border-cyan-300/50">
                <span class="text-sm font-bold uppercase tracking-widest text-cyan-200">Библиотека</span>
                <h2 class="mt-2 text-xl font-black text-white">Избранные игры</h2>
                <p class="mt-2 text-sm text-slate-400">Быстрый переход к сохраненным играм.</p>
            </a>
            <a href="{{ route('pc.index') }}" class="rounded-lg border border-white/10 bg-white/5 p-5 transition hover:-translate-y-1 hover:border-cyan-300/50">
                <span class="text-sm font-bold uppercase tracking-widest text-cyan-200">Железо</span>
                <h2 class="mt-2 text-xl font-black text-white">Проверка ПК</h2>
                <p class="mt-2 text-sm text-slate-400">Сравнить конфигурацию с требованиями игр.</p>
            </a>
            <a href="{{ route('recommendations') }}" class="rounded-lg border border-white/10 bg-white/5 p-5 transition hover:-translate-y-1 hover:border-cyan-300/50">
                <span class="text-sm font-bold uppercase tracking-widest text-cyan-200">Подбор</span>
                <h2 class="mt-2 text-xl font-black text-white">Рекомендации</h2>
                <p class="mt-2 text-sm text-slate-400">Игры на основе интересов и активности.</p>
            </a>
        </div>

        <div class="hub-panel p-5">
            <div class="mb-5 flex items-center justify-between gap-3">
                <h2 class="text-2xl font-black text-white">Последнее избранное</h2>
                <a href="{{ route('favorites.index') }}" class="text-sm font-semibold text-cyan-300 hover:text-white">Все</a>
            </div>
            @if($favoriteGames->count())
                <div class="grid grid-cols-1 gap-4 min-[430px]:grid-cols-2 md:grid-cols-4">
                    @foreach($favoriteGames as $game)
                        <x-game-card :game="$game" />
                    @endforeach
                </div>
            @else
                <div class="rounded-lg border border-white/10 bg-white/5 p-5 text-slate-400">
                    В избранном пока пусто. Открой каталог и добавь игры, которые хочешь сохранить.
                </div>
            @endif
        </div>

        <div class="hub-panel p-5">
            <h2 class="mb-5 text-2xl font-black text-white">Последние отзывы</h2>
            <div class="space-y-3">
                @forelse($recentReviews as $review)
                    <a href="{{ route('games.show', $review->game->slug) }}" class="block rounded-lg border border-white/10 bg-white/5 p-4 transition hover:border-cyan-300/50">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <b class="text-white">{{ $review->game->title }}</b>
                            <span class="rounded bg-cyan-400/15 px-2 py-1 text-sm font-black text-cyan-200">{{ $review->rating }}/10</span>
                        </div>
                        <p class="mt-2 line-clamp-2 text-sm text-slate-400">{{ $review->text }}</p>
                        <p class="mt-2 text-xs text-slate-500">{{ ['pending' => 'На модерации', 'approved' => 'Одобрен', 'rejected' => 'Отклонен'][$review->status] ?? $review->status }}</p>
                    </a>
                @empty
                    <p class="rounded-lg border border-white/10 bg-white/5 p-5 text-slate-400">Отзывов пока нет.</p>
                @endforelse
            </div>
        </div>
    </div>

    <aside class="space-y-6">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="hub-panel grid gap-4 p-5">
            @csrf
            @method('PATCH')
            <div>
                <h2 class="text-2xl font-black text-white">Настройки профиля</h2>
                <p class="mt-1 text-sm text-slate-400">Имя, email, аватар и смена пароля.</p>
            </div>
            <label class="grid gap-1">
                <span class="hub-label">Имя</span>
                <input name="name" value="{{ old('name', $user->name) }}" class="hub-input" placeholder="Имя">
            </label>
            <label class="grid gap-1">
                <span class="hub-label">Email</span>
                <input name="email" value="{{ old('email', $user->email) }}" class="hub-input" placeholder="Email">
            </label>
            <label class="grid gap-1">
                <span class="hub-label">URL аватара</span>
                <input name="avatar" value="{{ old('avatar', $user->avatar) }}" class="hub-input" placeholder="https://...">
            </label>
            <label class="grid gap-2 rounded-lg border border-white/10 bg-white/5 p-4">
                <span class="hub-label">Загрузить новый аватар</span>
                <span class="text-xs text-slate-500">JPG, PNG или WebP до 5 MB. Загруженный файл заменит URL выше.</span>
                <span class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <span class="hub-btn cursor-pointer">
                        Загрузить аватар
                        <input id="avatar-file-input" name="avatar_file" type="file" accept="image/*" class="sr-only">
                    </span>
                    <span id="avatar-file-name" class="min-h-10 rounded-md border border-white/10 bg-slate-950/70 px-3 py-2 text-sm text-slate-400">Файл не выбран</span>
                </span>
            </label>
            <div class="border-t border-white/10 pt-4">
                <p class="mb-3 text-sm font-bold uppercase tracking-widest text-cyan-200">Смена пароля</p>
                <div class="grid gap-3">
                    <input name="password" type="password" class="hub-input" placeholder="Новый пароль">
                    <input name="password_confirmation" type="password" class="hub-input" placeholder="Повторите пароль">
                </div>
            </div>
            @if($errors->any())
                <div class="rounded-md border border-red-400/30 bg-red-500/10 p-3 text-sm text-red-200">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <button class="hub-btn w-full">Сохранить профиль</button>
        </form>

        <div class="hub-panel p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-xl font-black text-white">Мои ПК</h2>
                <a href="{{ route('pc.index') }}" class="text-sm font-semibold text-cyan-300 hover:text-white">Настроить</a>
            </div>
            <div class="space-y-3">
                @forelse($pcConfigs as $config)
                    <div class="rounded-lg border border-white/10 bg-white/5 p-4">
                        <b class="text-white">{{ $config->title ?: 'Мой ПК' }}</b>
                        <p class="mt-2 text-sm text-slate-400">{{ $config->cpu }} / {{ $config->gpu }}</p>
                        <p class="mt-1 text-sm text-slate-500">{{ $config->ram }} GB RAM · {{ $config->storage }} GB · {{ $config->os }}</p>
                    </div>
                @empty
                    <p class="rounded-lg border border-white/10 bg-white/5 p-4 text-sm text-slate-400">Конфигураций пока нет.</p>
                @endforelse
            </div>
        </div>
    </aside>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const avatarInput = document.getElementById('avatar-file-input');
        const avatarFileName = document.getElementById('avatar-file-name');

        avatarInput?.addEventListener('change', () => {
            avatarFileName.textContent = avatarInput.files?.[0]?.name || 'Файл не выбран';
            avatarFileName.classList.toggle('text-cyan-100', Boolean(avatarInput.files?.length));
            avatarFileName.classList.toggle('text-slate-400', !avatarInput.files?.length);
        });
    });
</script>
@endpush

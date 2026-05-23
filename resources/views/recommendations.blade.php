@extends('layouts.app')

@section('content')
<section class="hub-container py-8 md:py-10">
    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <div>
            <p class="text-sm font-bold uppercase tracking-widest text-cyan-300">Rule-based подборка</p>
            <h1 class="mt-2 text-3xl font-black text-white">Персональные рекомендации</h1>
            <p class="mt-3 max-w-3xl text-slate-400">
                Рекомендации становятся точнее после избранного, отзывов и сохранённой конфигурации ПК. Добавьте 2-3 игры в избранное, чтобы алгоритм лучше понял ваши жанры.
            </p>
        </div>
        <aside class="hub-panel p-5">
            <h2 class="font-black text-white">Как это работает</h2>
            <div class="mt-4 grid gap-3 text-sm text-slate-300">
                <p><span class="text-cyan-300">+40</span> за жанры из избранного</p>
                <p><span class="text-cyan-300">+25</span> за похожесть на оценённые игры</p>
                <p><span class="text-cyan-300">+15</span> если игра подходит под ПК</p>
                <p><span class="text-cyan-300">+10</span> за хорошую цену относительно рейтинга</p>
            </div>
        </aside>
    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($recommendations as $item)
            @php
                $game = $item['game'];
            @endphp
            <article class="hub-panel overflow-hidden">
                <a href="{{ route('games.show', $game->slug) }}" class="grid h-full min-[430px]:grid-cols-[140px_1fr] sm:grid-cols-[160px_1fr]">
                    <img src="{{ $game->cover }}" alt="{{ $game->title }}" class="h-64 w-full object-cover object-top min-[430px]:h-full">
                    <div class="flex min-h-56 flex-col p-4">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="text-lg font-black leading-snug text-white">{{ $game->title }}</h2>
                            <span class="rounded bg-cyan-400/15 px-2 py-1 text-sm font-black text-cyan-200">{{ $item['score'] }}</span>
                        </div>
                        <p class="mt-2 line-clamp-1 text-xs text-slate-400">{{ $game->genres->pluck('name')->join(' • ') }}</p>
                        <p class="mt-4 text-sm leading-6 text-slate-300">{{ $item['reason'] }}</p>
                        <span class="mt-auto pt-4 text-sm font-semibold text-cyan-300">Открыть игру</span>
                    </div>
                </a>
            </article>
        @endforeach
    </div>
</section>
@endsection

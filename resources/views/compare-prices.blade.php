@extends('layouts.app')

@section('content')
@php
    $priceLabel = fn ($price) => $price['is_available']
        ? ((float) $price['price'] === 0.0 ? 'Бесплатно' : number_format((float) $price['price'], 0, '.', ' ') . ' ' . $price['currency'])
        : 'Недоступно';
    $bestLabel = fn ($best) => $best
        ? ((float) $best->price === 0.0 ? 'Бесплатно' : number_format((float) $best->price, 0, '.', ' ') . ' ' . $best->currency)
        : 'Нет';
@endphp
<section class="hub-container py-10">
    <div class="mb-8 max-w-3xl">
        <p class="text-sm font-bold uppercase tracking-widest text-cyan-300">Steam / Epic Games</p>
        <h1 class="mt-2 text-3xl font-black text-white">Скидки и цены</h1>
        <p class="mt-3 text-slate-400">Это обзорная витрина. Основное сравнение цен находится на странице каждой игры в блоке “Где купить”.</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($rows as $row)
            @php($game = $row['game'])
            <article class="hub-panel overflow-hidden transition duration-300 hover:-translate-y-1 hover:border-cyan-300/60 hover:shadow-[0_0_28px_rgba(34,211,238,0.18)]">
                <a href="{{ route('games.show', $game->slug) }}" class="grid h-full sm:grid-cols-[150px_1fr]">
                    <div class="aspect-[3/4] bg-slate-900 sm:h-full sm:aspect-auto">
                        <img src="{{ $game->cover }}" alt="{{ $game->title }}" class="h-full w-full object-cover object-top">
                    </div>
                    <div class="flex min-h-64 flex-col p-4">
                        <h2 class="text-lg font-black leading-snug text-white">{{ $game->title }}</h2>
                        <p class="mt-2 line-clamp-1 text-xs text-slate-400">{{ $game->genres->pluck('name')->join(' • ') }}</p>

                        <div class="mt-4 grid gap-2">
                            @foreach($row['prices'] as $price)
                                <div class="flex items-center justify-between rounded bg-white/5 px-3 py-2 text-sm">
                                    <span class="{{ $price['is_best'] ? 'text-cyan-200' : 'text-slate-300' }}">{{ $price['store'] }}</span>
                                    <span class="font-bold text-white">
                                        {{ $priceLabel($price) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-auto flex items-end justify-between gap-3 pt-5">
                            <div>
                                <p class="text-xs uppercase tracking-widest text-slate-500">Лучшая цена</p>
                                <p class="text-xl font-black text-cyan-300">{{ $bestLabel($row['best']) }}</p>
                            </div>
                            <span class="text-sm font-semibold text-cyan-300">Открыть игру</span>
                        </div>
                    </div>
                </a>
            </article>
        @endforeach
    </div>
</section>
@endsection

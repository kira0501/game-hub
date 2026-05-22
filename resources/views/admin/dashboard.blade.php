@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-black text-white">Dashboard</h1>
<div class="mt-6 grid gap-4 md:grid-cols-3">
    @foreach([['Игры',$gamesCount],['Пользователи',$usersCount],['Отзывы',$reviewsCount]] as [$label,$value])
        <div class="rounded-lg border border-white/10 bg-white/5 p-5"><p class="text-sm text-slate-400">{{ $label }}</p><p class="mt-2 text-3xl font-black text-cyan-300">{{ $value }}</p></div>
    @endforeach
</div>
<div class="mt-8 grid gap-6 lg:grid-cols-2">
    <section class="rounded-lg border border-white/10 bg-white/5 p-5"><h2 class="mb-4 text-xl font-bold">Топ игр</h2>@foreach($topGames as $game)<p class="flex justify-between border-b border-white/10 py-2"><span>{{ $game->title }}</span><b>{{ $game->user_score_avg }}</b></p>@endforeach</section>
    <section class="rounded-lg border border-white/10 bg-white/5 p-5"><h2 class="mb-4 text-xl font-bold">Популярные жанры</h2>@foreach($popularGenres as $genre)<p class="flex justify-between border-b border-white/10 py-2"><span>{{ $genre->name }}</span><b>{{ $genre->games_count }}</b></p>@endforeach</section>
    <section class="rounded-lg border border-white/10 bg-white/5 p-5"><h2 class="mb-4 text-xl font-bold">Игры без цен</h2>@forelse($gamesWithoutPrices as $game)<p class="border-b border-white/10 py-2">{{ $game->title }}</p>@empty<p class="text-slate-400">Все игры имеют цены.</p>@endforelse</section>
    <section class="rounded-lg border border-white/10 bg-white/5 p-5"><h2 class="mb-4 text-xl font-bold">Последние игры</h2>@foreach($latestGames as $game)<p class="border-b border-white/10 py-2">{{ $game->title }}</p>@endforeach</section>
</div>
@endsection

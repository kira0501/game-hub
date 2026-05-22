@extends('layouts.app')

@section('content')
<section class="hub-container py-10">
    <h1 class="text-3xl font-black text-white">{{ $genre->name }}</h1>
    <p class="mt-2 text-slate-400">Игры выбранного жанра.</p>
    <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-6">
        @forelse($games as $game)
            <x-game-card :game="$game" />
        @empty
            <div class="hub-panel col-span-full p-10 text-center text-slate-400">В жанре пока нет игр.</div>
        @endforelse
    </div>
    <div class="mt-8">{{ $games->links() }}</div>
</section>
@endsection

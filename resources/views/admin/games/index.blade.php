@extends('layouts.admin')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
    <div>
        <h1 class="text-3xl font-black">Игры</h1>
        <p class="mt-1 text-sm text-slate-400">Поиск работает по названию, slug, студии, издателю и жанрам.</p>
    </div>
    <a class="hub-btn" href="{{ route('admin.games.create') }}">Добавить</a>
</div>

<form method="GET" action="{{ route('admin.games.index') }}" class="mt-6 grid gap-3 rounded-lg border border-white/10 bg-white/5 p-4 md:grid-cols-[minmax(0,1fr)_auto_auto]">
    <label class="sr-only" for="admin-game-search">Поиск игр</label>
    <input id="admin-game-search" name="q" value="{{ request('q') }}" class="hub-input" placeholder="Найти игру, жанр, разработчика или издателя">
    <button class="hub-btn">Найти</button>
    @if(request()->filled('q'))
        <a href="{{ route('admin.games.index') }}" class="hub-btn-secondary">Сбросить</a>
    @endif
</form>

<div class="mt-6 overflow-hidden rounded-lg border border-white/10">
    <table class="w-full text-left text-sm">
        <thead class="bg-white/10">
            <tr>
                <th class="p-3">Название</th>
                <th class="p-3">Жанры</th>
                <th class="p-3">Оценка</th>
                <th class="p-3">Статус</th>
                <th class="p-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
        @forelse($games as $game)
            <tr class="align-middle">
                <td class="p-3 font-bold">{{ $game->title }}</td>
                <td class="p-3 text-slate-400">{{ $game->genres->pluck('name')->join(', ') ?: 'Без жанров' }}</td>
                <td class="p-3">{{ $game->user_score_avg }}</td>
                <td class="p-3">
                    <span @class([
                        'inline-flex rounded-md px-2.5 py-1 text-xs font-bold',
                        'bg-cyan-400/15 text-cyan-200' => $game->is_active,
                        'bg-amber-400/15 text-amber-200' => ! $game->is_active,
                    ])>
                        {{ $game->is_active ? 'Активна' : 'Скрыта' }}
                    </span>
                </td>
                <td class="p-3">
                    <div class="flex flex-wrap justify-end gap-2">
                        <a class="rounded-md bg-cyan-500 px-3 py-2 text-sm font-bold text-slate-950 transition hover:bg-cyan-300" href="{{ route('admin.games.edit', $game) }}">Редактировать</a>

                        <form method="POST" action="{{ route('admin.games.toggle-active', $game) }}">
                            @csrf
                            @method('PATCH')
                            <button class="rounded-md bg-amber-400 px-3 py-2 text-sm font-bold text-slate-950 transition hover:bg-amber-300">
                                {{ $game->is_active ? 'Скрыть' : 'Показать' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.games.destroy', $game) }}" onsubmit="return confirm('Удалить игру {{ addslashes($game->title) }} окончательно? Лучше использовать кнопку Скрыть, если игру нужно просто убрать из каталога.');">
                            @csrf
                            @method('DELETE')
                            <button class="rounded-md bg-red-500 px-3 py-2 text-sm font-bold text-white transition hover:bg-red-400">Удалить</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="p-6 text-center text-slate-400">Игры не найдены.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div class="mt-6">{{ $games->links() }}</div>
@endsection

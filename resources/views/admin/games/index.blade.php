@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between"><h1 class="text-3xl font-black">Игры</h1><a class="hub-btn" href="{{ route('admin.games.create') }}">Добавить</a></div>
<div class="mt-6 overflow-hidden rounded-lg border border-white/10">
    <table class="w-full text-left text-sm"><thead class="bg-white/10"><tr><th class="p-3">Название</th><th class="p-3">Жанры</th><th class="p-3">Оценка</th><th class="p-3">Статус</th><th class="p-3"></th></tr></thead><tbody class="divide-y divide-white/10">
        @foreach($games as $game)<tr><td class="p-3 font-bold">{{ $game->title }}</td><td class="p-3 text-slate-400">{{ $game->genres->pluck('name')->join(', ') }}</td><td class="p-3">{{ $game->user_score_avg }}</td><td class="p-3">{{ $game->is_active ? 'Активна' : 'Скрыта' }}</td><td class="p-3 flex gap-2"><a class="text-cyan-300" href="{{ route('admin.games.edit',$game) }}">Ред.</a><form method="POST" action="{{ route('admin.games.destroy',$game) }}">@csrf @method('DELETE')<button class="text-red-300">Удал.</button></form></td></tr>@endforeach
    </tbody></table>
</div>
<div class="mt-6">{{ $games->links() }}</div>
@endsection

@extends('layouts.app')

@section('content')
<section class="hub-container py-8 md:py-10">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-white">Избранное</h1>
            <p class="mt-2 text-slate-400">Отмечайте игры галочками и убирайте лишнее одним действием.</p>
        </div>
    </div>

    @if($games->count())
        <form method="POST" action="{{ route('favorites.destroy-selected') }}" class="mt-8">@csrf @method('DELETE')
            <div class="mb-4 grid gap-3 rounded-lg border border-white/10 bg-white/5 p-3 sm:flex sm:flex-wrap sm:items-center sm:justify-between">
                <button class="hub-btn-secondary" type="button" id="select-all-favorites">Выбрать все</button>
                <button class="hub-btn" type="submit">Убрать выбранные</button>
            </div>
            <div class="grid grid-cols-1 gap-4 min-[430px]:grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
                @foreach($games as $game)
                    <article class="hub-panel group relative overflow-hidden transition duration-300 hover:-translate-y-1 hover:border-cyan-300/70 hover:shadow-[0_0_28px_rgba(34,211,238,0.22)]">
                        <label class="absolute left-3 top-3 z-10 rounded bg-black/70 px-2 py-1 text-xs text-white">
                            <input type="checkbox" name="game_ids[]" value="{{ $game->id }}" class="favorite-checkbox rounded bg-black/40"> выбрать
                        </label>
                        <a href="{{ route('games.show', $game->slug) }}" class="block">
                            <div class="aspect-[3/4] overflow-hidden bg-slate-900">
                                <img src="{{ $game->cover }}" alt="{{ $game->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                            </div>
                            <div class="space-y-3 p-4">
                                <h3 class="line-clamp-1 font-bold text-white">{{ $game->title }}</h3>
                                <p class="line-clamp-1 text-xs text-slate-400">{{ $game->genres->pluck('name')->join(' • ') }}</p>
                            </div>
                        </a>
                        <div class="px-4 pb-4">
                            <button name="game_ids[]" value="{{ $game->id }}" class="favorite-remove-one w-full rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-sm font-semibold text-red-200 hover:bg-red-500/20">Убрать</button>
                        </div>
                    </article>
                @endforeach
            </div>
        </form>
        <div class="mt-8">{{ $games->links() }}</div>
    @else
        <div class="hub-panel mt-8 p-10 text-center text-slate-400">Вы пока не добавили игры в избранное.</div>
    @endif
</section>

@push('scripts')
<script>
    document.getElementById('select-all-favorites')?.addEventListener('click', () => {
        const boxes = Array.from(document.querySelectorAll('.favorite-checkbox'));
        const shouldCheck = boxes.some((box) => !box.checked);
        boxes.forEach((box) => box.checked = shouldCheck);
    });

    document.querySelectorAll('.favorite-remove-one').forEach((button) => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.favorite-checkbox').forEach((box) => box.checked = false);
        });
    });
</script>
@endpush
@endsection

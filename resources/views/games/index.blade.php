@extends('layouts.app')

@section('content')
@php
    $selectedGenre = $genres->firstWhere('slug', request('genre'));
    $sortOptions = [
        '' => 'По рейтингу',
        'new' => 'Новинки',
        'price' => 'Дешевле',
        'title' => 'По названию',
    ];
    $selectedSort = $sortOptions[request('sort', '')] ?? 'По рейтингу';
@endphp
<section class="hub-container py-8 md:py-10">
    <div class="mb-6">
        <h1 class="text-3xl font-black text-white">Каталог игр</h1>
        <p class="mt-2 text-slate-400">Поиск, жанры, оценка, цена и сортировка.</p>
    </div>
    <form class="hub-panel mb-8 grid gap-3 p-4 sm:gap-4 md:grid-cols-5">
        <input name="q" value="{{ request('q') }}" class="hub-input" placeholder="Название">
        <div class="custom-select relative">
            <input type="hidden" name="genre" value="{{ request('genre') }}">
            <button type="button" class="custom-select-button flex h-10 w-full items-center justify-between rounded-md border border-white/10 bg-black/30 px-3 text-left text-slate-100 transition hover:border-cyan-300 focus:border-cyan-300">
                <span>{{ $selectedGenre?->name ?? 'Все жанры' }}</span>
                <span class="text-slate-400">⌄</span>
            </button>
            <div class="custom-select-menu invisible absolute left-0 top-full z-30 mt-2 max-h-64 w-full translate-y-1 overflow-y-auto rounded-md border border-cyan-300/40 bg-slate-950 p-1 opacity-0 shadow-2xl shadow-black/50 transition sm:max-h-72">
                <button type="button" data-value="" class="custom-select-option w-full rounded px-3 py-2 text-left text-sm text-slate-100 hover:bg-cyan-400/15 {{ request('genre') ? '' : 'bg-cyan-500 text-slate-950 hover:bg-cyan-500' }}">Все жанры</button>
                @foreach($genres as $genre)
                    <button type="button" data-value="{{ $genre->slug }}" class="custom-select-option w-full rounded px-3 py-2 text-left text-sm text-slate-100 hover:bg-cyan-400/15 {{ request('genre') === $genre->slug ? 'bg-cyan-500 text-slate-950 hover:bg-cyan-500' : '' }}">{{ $genre->name }}</button>
                @endforeach
            </div>
        </div>
        <input name="min_score" value="{{ request('min_score') }}" class="hub-input" placeholder="Оценка от 8">
        <input name="max_price" value="{{ request('max_price') }}" class="hub-input" placeholder="Цена до">
        <div class="custom-select relative">
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <button type="button" class="custom-select-button flex h-10 w-full items-center justify-between rounded-md border border-white/10 bg-black/30 px-3 text-left text-slate-100 transition hover:border-cyan-300 focus:border-cyan-300">
                <span>{{ $selectedSort }}</span>
                <span class="text-slate-400">⌄</span>
            </button>
            <div class="custom-select-menu invisible absolute left-0 top-full z-30 mt-2 w-full translate-y-1 rounded-md border border-cyan-300/40 bg-slate-950 p-1 opacity-0 shadow-2xl shadow-black/50 transition">
                @foreach($sortOptions as $value => $label)
                    <button type="button" data-value="{{ $value }}" class="custom-select-option w-full rounded px-3 py-2 text-left text-sm text-slate-100 hover:bg-cyan-400/15 {{ request('sort', '') === $value ? 'bg-cyan-500 text-slate-950 hover:bg-cyan-500' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>
        <button class="hub-btn md:col-span-5">Фильтровать</button>
    </form>
    @if($games->count())
        <div class="grid grid-cols-1 gap-4 min-[430px]:grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
            @foreach($games as $game)<x-game-card :game="$game" />@endforeach
        </div>
        <div class="mt-8">{{ $games->links() }}</div>
    @else
        <div class="hub-panel p-10 text-center text-slate-400">Игры не найдены. Попробуйте изменить фильтры.</div>
    @endif
</section>
@push('scripts')
<style>
    .custom-select-menu {
        scrollbar-color: rgba(103, 232, 249, .7) rgba(15, 23, 42, .95);
        scrollbar-width: thin;
    }

    .custom-select-menu::-webkit-scrollbar {
        width: 10px;
    }

    .custom-select-menu::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, .95);
    }

    .custom-select-menu::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: rgba(103, 232, 249, .7);
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.custom-select').forEach((select) => {
            const button = select.querySelector('.custom-select-button');
            const menu = select.querySelector('.custom-select-menu');
            const hidden = select.querySelector('input[type="hidden"]');
            const label = button.querySelector('span');

            button.addEventListener('click', () => {
                document.querySelectorAll('.custom-select-menu').forEach((other) => {
                    if (other !== menu) other.classList.add('invisible', 'opacity-0', 'translate-y-1');
                });
                menu.classList.toggle('invisible');
                menu.classList.toggle('opacity-0');
                menu.classList.toggle('translate-y-1');
            });

            menu.querySelectorAll('.custom-select-option').forEach((option) => {
                option.addEventListener('click', () => {
                    hidden.value = option.dataset.value;
                    label.textContent = option.textContent.trim();
                    menu.classList.add('invisible', 'opacity-0', 'translate-y-1');
                });
            });
        });

        document.addEventListener('click', (event) => {
            if (event.target.closest('.custom-select')) return;
            document.querySelectorAll('.custom-select-menu').forEach((menu) => {
                menu.classList.add('invisible', 'opacity-0', 'translate-y-1');
            });
        });
    });
</script>
@endpush
@endsection

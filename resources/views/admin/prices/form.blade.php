@extends('layouts.admin')

@section('content')
<div class="max-w-5xl">
    <a href="{{ route('admin.prices.index') }}" class="text-sm text-cyan-300 hover:text-white">← Назад к ценам</a>
    <h1 class="mt-3 text-3xl font-black">Цены: {{ $game->title }}</h1>
    <p class="mt-2 text-sm text-slate-400">Если игра недоступна в магазине, выключи доступность и оставь цену пустой.</p>

    <form method="POST" action="{{ route('admin.prices.games.update', $game) }}" class="mt-6 grid gap-5">
        @csrf
        @method('PUT')

        @foreach($stores as $store)
            @php
                $price = $game->prices->firstWhere('store_id', $store->id);
            @endphp
            <section class="rounded-lg border border-white/10 bg-white/5 p-5">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-xl font-black text-white">{{ $store->name }}</h2>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="prices[{{ $store->id }}][is_available]" value="1" @checked(old("prices.{$store->id}.is_available", $price?->is_available ?? false))>
                        Доступна
                    </label>
                </div>
                <div class="grid gap-4 md:grid-cols-4">
                    <input name="prices[{{ $store->id }}][price]" type="number" step="0.01" value="{{ old("prices.{$store->id}.price", $price?->price) }}" class="hub-input" placeholder="Цена">
                    <input name="prices[{{ $store->id }}][currency]" value="{{ old("prices.{$store->id}.currency", $price?->currency ?? 'RUB') }}" class="hub-input" placeholder="Валюта">
                    <input name="prices[{{ $store->id }}][discount_percent]" type="number" min="0" max="100" value="{{ old("prices.{$store->id}.discount_percent", $price?->discount_percent ?? 0) }}" class="hub-input" placeholder="Скидка %">
                    <input name="prices[{{ $store->id }}][external_url]" value="{{ old("prices.{$store->id}.external_url", $price?->external_url) }}" class="hub-input md:col-span-1" placeholder="Ссылка">
                </div>
            </section>
        @endforeach

        @if($errors->any())
            <div class="rounded border border-red-400/30 bg-red-500/10 p-3 text-red-100">{{ $errors->first() }}</div>
        @endif

        <button class="hub-btn w-fit">Сохранить цены</button>
    </form>
</div>
@endsection

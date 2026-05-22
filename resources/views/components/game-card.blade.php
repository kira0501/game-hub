@props(['game', 'reason' => null])
@php
    $cover = $game->cover
        ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=80';
    $best = $game->prices->where('is_available', true)->whereNotNull('price')->sortBy('price')->first();
    $priceLabel = $best ? ((float) $best->price === 0.0 ? 'Бесплатно' : number_format((float) $best->price, 0, '.', ' ') . ' ' . $best->currency) : 'Нет цены';
@endphp
<article class="group overflow-hidden rounded-lg border border-white/10 bg-hub-panel transition duration-300 hover:-translate-y-1 hover:border-cyan-300/70 hover:shadow-[0_0_28px_rgba(34,211,238,0.22)]">
    <a href="{{ route('games.show', $game->slug) }}" class="block">
        <div class="aspect-[3/4] overflow-hidden bg-slate-900">
            <img src="{{ $cover }}" alt="{{ $game->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        </div>
        <div class="space-y-3 p-3 sm:p-4">
            <div>
                <h3 class="line-clamp-1 font-bold text-white">{{ $game->title }}</h3>
                <p class="mt-1 line-clamp-1 text-xs text-slate-400">{{ $game->genres->pluck('name')->join(' • ') }}</p>
            </div>
            <div class="flex items-center justify-between gap-3">
                <span class="rounded bg-cyan-400/15 px-2 py-1 text-sm font-bold text-cyan-200">{{ number_format((float) $game->user_score_avg, 1) }}/10</span>
                <span class="text-right text-sm font-semibold leading-tight text-slate-200">{{ $priceLabel }}</span>
            </div>
            @if($reason)
                <p class="line-clamp-2 text-xs text-slate-400">{{ $reason }}</p>
            @endif
        </div>
    </a>
</article>

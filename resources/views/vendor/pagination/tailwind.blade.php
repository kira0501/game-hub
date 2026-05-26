@if ($paginator->hasPages())
    @php
        $pageUrl = function (?string $url): ?string {
            if (! $url) {
                return $url;
            }

            $parts = parse_url($url);

            return isset($parts['query']) ? '?'.$parts['query'] : $url;
        };
    @endphp

    <nav role="navigation" aria-label="Пагинация" class="flex flex-col gap-4 border-t border-white/10 pt-6 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm font-medium text-slate-400">
            Показано
            <span class="font-bold text-white">{{ $paginator->firstItem() }}</span>
            -
            <span class="font-bold text-white">{{ $paginator->lastItem() }}</span>
            из
            <span class="font-bold text-white">{{ $paginator->total() }}</span>
        </p>

        <div class="flex flex-wrap items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex min-h-10 items-center justify-center rounded-md border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-500">Назад</span>
            @else
                <a href="{{ $pageUrl($paginator->previousPageUrl()) }}" rel="prev" class="inline-flex min-h-10 items-center justify-center rounded-md border border-cyan-300/50 bg-cyan-500 px-4 py-2 text-sm font-black text-white shadow-[0_0_18px_rgba(34,211,238,0.18)] transition hover:bg-cyan-400 hover:shadow-[0_0_24px_rgba(34,211,238,0.32)] focus:outline-none focus:ring-2 focus:ring-cyan-300">Назад</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-md border border-white/10 bg-white/5 px-3 text-sm font-bold text-slate-500">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex h-10 min-w-10 items-center justify-center rounded-md border border-cyan-200 bg-cyan-500 px-3 text-sm font-black text-white shadow-[0_0_20px_rgba(34,211,238,0.35)]">{{ $page }}</span>
                        @else
                            <a href="{{ $pageUrl($url) }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-md border border-cyan-300/40 bg-slate-950/70 px-3 text-sm font-black text-white transition hover:border-cyan-200 hover:bg-cyan-500 hover:shadow-[0_0_18px_rgba(34,211,238,0.24)] focus:outline-none focus:ring-2 focus:ring-cyan-300">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $pageUrl($paginator->nextPageUrl()) }}" rel="next" class="inline-flex min-h-10 items-center justify-center rounded-md border border-cyan-300/50 bg-cyan-500 px-4 py-2 text-sm font-black text-white shadow-[0_0_18px_rgba(34,211,238,0.18)] transition hover:bg-cyan-400 hover:shadow-[0_0_24px_rgba(34,211,238,0.32)] focus:outline-none focus:ring-2 focus:ring-cyan-300">Далее</a>
            @else
                <span class="inline-flex min-h-10 items-center justify-center rounded-md border border-white/10 bg-white/5 px-4 py-2 text-sm font-semibold text-slate-500">Далее</span>
            @endif
        </div>
    </nav>
@endif

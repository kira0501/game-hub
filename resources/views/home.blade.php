@extends('layouts.app')

@section('content')
@php
    $placeholderWide = 'https://images.unsplash.com/photo-1550745165-9bc0b252726f?auto=format&fit=crop&w=1600&q=80';
    $placeholderCover = 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=80';
    $galleryImages = fn ($game) => $game->media->where('type', 'image')->where('role', 'gallery')->values();
    $mediaImage = fn ($game) => $game->hero_image
        ?: $galleryImages($game)->first()?->url
        ?: $game->cover
        ?: $placeholderWide;
    $carouselImage = fn ($game) => $game->carousel_image ?: $game->hero_image ?: $mediaImage($game);
    $coverImage = fn ($game) => $game->cover
        ?: $placeholderCover;
    $mediaImages = fn ($game) => $galleryImages($game)
        ->reject(fn ($media) => in_array($media->url, array_filter([$game->cover, $game->hero_image, $game->carousel_image]), true))
        ->take(4)
        ->values();
    $priceLabel = fn ($price) => $price ? ((float) $price->price === 0.0 ? 'Бесплатно' : number_format((float) $price->price, 0, '.', ' ') . ' ' . $price->currency) : 'Нет цены';
    $smartGame = $recommended->first()['game'] ?? $popularGames->first();
    $smartBest = $smartGame?->prices->where('is_available', true)->whereNotNull('price')->sortBy('price')->first();
    $heroGames = ($carouselGames ?? collect())->isNotEmpty() ? $carouselGames : $popularGames;
@endphp
<section class="relative overflow-hidden border-b border-white/10">
    <div class="absolute inset-0">
        <img id="home-hero-bg" src="{{ $heroGames->first() ? $carouselImage($heroGames->first()) : $placeholderWide }}" class="h-full w-full object-cover opacity-30 transition duration-700" alt="">
        <div class="absolute inset-0 bg-gradient-to-r from-hub-bg via-hub-bg/85 to-hub-bg/50"></div>
        <div class="absolute inset-0 bg-black/25"></div>
    </div>
    <div class="hub-container relative grid min-h-[520px] content-center gap-8 py-8 md:min-h-[650px] md:py-12">
        <div class="max-w-2xl">
            <p class="mb-4 text-sm font-bold uppercase tracking-widest text-cyan-300">Библиотека видеоигр</p>
            <h1 class="text-4xl font-black leading-tight text-white md:text-6xl">Game Hub</h1>
            <p class="mt-5 text-base text-slate-300 md:text-lg">Каталог игр с рекомендациями, проверкой ПК, отзывами игроков и сравнением цен в Steam и Epic Games.</p>
            <div class="mt-8 grid gap-3 sm:flex sm:flex-wrap">
                <a href="{{ route('games.index') }}" class="hub-btn">Смотреть каталог</a>
                <a href="{{ route('prices.compare') }}" class="hub-btn-secondary">Скидки и цены</a>
            </div>
        </div>
        @if($heroGames->count())
            <div class="steam-carousel" id="hero-carousel">
                <div class="steam-carousel-title">Популярное и рекомендуемое</div>
                <div class="steam-carousel-frame">
                    @foreach($heroGames->take(6)->values() as $index => $game)
                        @php
                            $best = $game->prices->where('is_available', true)->whereNotNull('price')->sortBy('price')->first();
                            $defaultSlideImage = $carouselImage($game);
                        @endphp
                        <article class="hero-slide {{ $index === 0 ? 'is-active' : '' }}" data-slide="{{ $index }}" data-bg="{{ $defaultSlideImage }}" data-default-image="{{ $defaultSlideImage }}">
                            <div class="steam-slide-grid">
                                <a href="{{ route('games.show', $game->slug) }}" class="steam-slide-image-link">
                                    <img src="{{ $defaultSlideImage }}" class="steam-slide-image" alt="{{ $game->title }}" data-main-slide-image>
                                </a>
                                <div class="steam-slide-info">
                                    <div class="steam-thumbs">
                                        @foreach($mediaImages($game) as $thumbIndex => $thumb)
                                            <button type="button" class="steam-thumb-button" data-thumb-url="{{ $thumb->url }}">
                                                <img src="{{ $thumb->url }}" alt="{{ $game->title }}">
                                            </button>
                                        @endforeach
                                    </div>
                                    <div class="steam-copy">
                                        <a href="{{ route('games.show', $game->slug) }}" class="steam-game-title">{{ $game->title }}</a>
                                        <p class="steam-game-genres">{{ $game->genres->pluck('name')->join(' • ') }}</p>
                                        <p class="steam-game-description">{{ \Illuminate\Support\Str::limit($game->description, 155) }}</p>
                                    </div>
                                    <div class="steam-slide-bottom">
                                        <span>{{ number_format((float) $game->user_score_avg, 1) }}/10</span>
                                        <strong>{{ $priceLabel($best) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                    <button type="button" data-carousel-prev class="steam-arrow steam-arrow-left" aria-label="Предыдущий слайд">‹</button>
                    <button type="button" data-carousel-next class="steam-arrow steam-arrow-right" aria-label="Следующий слайд">›</button>
                </div>
                <div class="steam-dots">
                    @foreach($heroGames->take(6)->values() as $index => $game)
                        <button type="button" data-carousel-dot="{{ $index }}" class="{{ $index === 0 ? 'is-active' : '' }}" aria-label="Слайд {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

<section class="hub-container space-y-12 py-12">
    <div class="grid gap-4 md:grid-cols-3">
        @foreach([['Игр в каталоге', $stats['games']], ['Отзывов', $stats['reviews']], ['Жанров', $stats['genres']]] as [$label, $value])
            <div class="rounded-lg border border-white/10 bg-white/5 p-5">
                <p class="text-sm text-slate-400">{{ $label }}</p>
                <p class="mt-2 text-3xl font-black text-cyan-300">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid items-stretch gap-6 xl:grid-cols-[1.15fr_.85fr]">
        <div class="hub-panel h-full overflow-hidden">
            <div class="grid h-full md:min-h-[360px] md:grid-cols-[minmax(0,1fr)_260px]">
                <div class="relative min-h-[300px] md:h-full md:min-h-[360px]">
                    <img src="{{ $smartGame ? $mediaImage($smartGame) : 'https://images.unsplash.com/photo-1511512578047-dfb367046420?auto=format&fit=crop&w=1200&q=80' }}" class="absolute inset-0 h-full w-full object-cover opacity-75" alt="">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-black/65 to-black/20"></div>
                    <div class="relative flex h-full min-h-[300px] flex-col justify-center p-5 md:min-h-[360px] md:p-6">
                        <p class="text-sm font-bold uppercase tracking-widest text-cyan-200 drop-shadow">Умный подбор</p>
                        <h2 class="mt-2 max-w-md text-2xl font-black text-white drop-shadow-lg md:text-3xl">Рекомендации не случайные</h2>
                        <p class="mt-3 max-w-xl text-slate-100 drop-shadow">Алгоритм учитывает жанры из избранного, оценки, цену, популярность и совместимость с твоим ПК.</p>
                        <a href="{{ route('recommendations') }}" class="hub-btn mt-5 w-fit">Открыть подборку</a>
                    </div>
                </div>
                @if($smartGame)
                    <a href="{{ route('games.show', $smartGame->slug) }}" class="group flex h-full min-h-[300px] flex-col border-t border-white/10 bg-slate-950/60 md:min-h-[360px] md:border-l md:border-t-0">
                        <img src="{{ $coverImage($smartGame) }}" class="h-52 w-full object-cover object-top transition duration-500 group-hover:scale-[1.02] md:h-48" alt="{{ $smartGame->title }}">
                        <div class="flex flex-1 flex-col p-5">
                            <span class="text-xs font-bold uppercase tracking-widest text-cyan-300">Игра для тебя</span>
                            <h3 class="mt-2 text-xl font-black text-white">{{ $smartGame->title }}</h3>
                            <p class="mt-2 line-clamp-1 text-sm text-slate-400">{{ $smartGame->genres->pluck('name')->join(' • ') }}</p>
                            <div class="mt-auto flex items-center justify-between gap-3 pt-5">
                                <span class="rounded bg-cyan-400/15 px-2 py-1 text-sm font-black text-cyan-200">{{ number_format((float) $smartGame->user_score_avg, 1) }}/10</span>
                                <span class="text-sm font-bold text-white">{{ $priceLabel($smartBest) }}</span>
                            </div>
                        </div>
                    </a>
                @endif
            </div>
        </div>
        <div class="grid gap-4 sm:grid-cols-2">
            <a href="{{ route('pc.index') }}" class="rounded-lg border border-cyan-400/30 bg-cyan-400/10 p-5 transition hover:bg-cyan-400/15">
                <span class="text-sm font-bold uppercase tracking-widest text-cyan-200">Проверка ПК</span>
                <h3 class="mt-3 text-xl font-black text-white">Узнай, запустится ли игра</h3>
                <p class="mt-2 text-sm text-slate-300">Сравнение CPU, GPU, RAM, места на диске и ОС с минимальными и рекомендуемыми требованиями.</p>
            </a>
            <a href="{{ route('prices.compare') }}" class="rounded-lg border border-white/10 bg-white/5 p-5 transition hover:border-cyan-300/50">
                <span class="text-sm font-bold uppercase tracking-widest text-cyan-200">Цены внутри игры</span>
                <h3 class="mt-3 text-xl font-black text-white">Steam и Epic на странице игры</h3>
                <p class="mt-2 text-sm text-slate-300">Основное сравнение цен теперь находится в блоке “Где купить” у каждой игры.</p>
            </a>
            <div class="rounded-lg border border-white/10 bg-white/5 p-5 sm:col-span-2">
                <span class="text-sm font-bold uppercase tracking-widest text-cyan-200">Для защиты</span>
                <h3 class="mt-3 text-xl font-black text-white">Полная MVC-структура</h3>
                <p class="mt-2 text-sm text-slate-300">Админка, CRUD, FormRequest-валидация, middleware ролей, Eloquent-связи, сидеры и сервисный слой.</p>
            </div>
        </div>
    </div>

    @if($dealGames->count())
        <div>
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-2xl font-black text-white">Выгодные предложения</h2>
                <a href="{{ route('prices.compare') }}" class="text-sm text-cyan-300 hover:text-white">Все скидки</a>
            </div>
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                @foreach($dealGames as $game)
                    @php
                        $best = $game->prices->where('is_available', true)->whereNotNull('price')->sortBy('price')->first();
                    @endphp
                    <a href="{{ route('games.show', $game->slug) }}" class="hub-panel group flex h-full min-h-[330px] flex-col overflow-hidden transition hover:-translate-y-1 hover:border-cyan-300/50 hover:shadow-[0_0_28px_rgba(34,211,238,0.22)]">
                        <div class="aspect-video overflow-hidden bg-slate-950">
                            <img src="{{ $coverImage($game) }}" class="h-full w-full object-cover object-top transition-all duration-500 group-hover:object-contain" alt="{{ $game->title }}">
                        </div>
                        <div class="flex flex-1 flex-col p-4 transition duration-300 group-hover:-translate-y-3 group-hover:opacity-0">
                            <h3 class="min-h-12 text-base font-black leading-snug text-white">{{ $game->title }}</h3>
                            <div class="mt-3 flex min-h-14 flex-wrap content-start gap-1.5">
                                @foreach($game->genres->take(3) as $genre)
                                    <span class="rounded bg-white/5 px-2 py-1 text-xs text-slate-300">{{ $genre->name }}</span>
                                @endforeach
                            </div>
                            <div class="mt-4 border-t border-white/10 pt-4">
                                <p class="text-xs uppercase tracking-widest text-slate-500">Лучшая цена</p>
                                <p class="mt-1 text-2xl font-black text-cyan-300">{{ $priceLabel($best) }}</p>
                            </div>
                            <span class="mt-auto pt-5 text-sm font-semibold text-cyan-300 group-hover:text-white">Смотреть игру</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @foreach([['Популярное', $popularGames], ['Новинки', $newGames]] as [$title, $items])
        <div>
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-2xl font-black text-white">{{ $title }}</h2>
                <a href="{{ route('games.index') }}" class="text-sm text-cyan-300 hover:text-white">Все игры</a>
            </div>
            <div class="grid grid-cols-1 gap-4 min-[430px]:grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
                @foreach($items as $game)
                    <x-game-card :game="$game" />
                @endforeach
            </div>
        </div>
    @endforeach

    <div>
        <h2 class="mb-5 text-2xl font-black text-white">Рекомендуем</h2>
        <div class="grid grid-cols-1 gap-4 min-[430px]:grid-cols-2 md:grid-cols-4 lg:grid-cols-6">
            @foreach($recommended as $item)
                <x-game-card :game="$item['game']" :reason="$item['reason']" />
            @endforeach
        </div>
    </div>

    <div>
        <h2 class="mb-5 text-2xl font-black text-white">По жанрам</h2>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            @foreach($genres as $genre)
                <a href="{{ route('genres.show', $genre->slug) }}" class="rounded-lg border border-white/10 bg-white/5 p-4 transition hover:-translate-y-1 hover:border-cyan-300/50">
                    <span class="font-bold text-white">{{ $genre->name }}</span>
                    <span class="block text-sm text-slate-400">{{ $genre->games_count }} игр</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

@push('scripts')
<style>
    .steam-carousel {
        width: min(100%, 940px);
        margin: 0 auto;
        position: relative;
    }

    .steam-carousel-title {
        margin-bottom: 10px;
        color: #e5edf5;
        font-size: 14px;
        font-weight: 800;
    }

    .steam-carousel-frame {
        position: relative;
        height: 350px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 4px;
        background: #111a24;
        box-shadow: 0 18px 40px rgba(0,0,0,.45);
    }

    .hero-slide {
        position: absolute;
        inset: 0;
        height: 100%;
        opacity: 0;
        pointer-events: none;
        transform: translateX(18px) scale(.985);
        transition: opacity .55s ease, transform .55s ease;
    }

    .hero-slide.is-active {
        opacity: 1;
        pointer-events: auto;
        transform: translateX(0) scale(1);
    }

    .steam-slide-grid {
        display: grid;
        grid-template-columns: 64% 36%;
        height: 100%;
    }

    .steam-slide-image-link {
        display: block;
        height: 100%;
        overflow: hidden;
        background: #0f172a;
    }

    .steam-slide-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        opacity: 1;
        transform: scale(1);
        transition: opacity .28s ease, transform .45s ease;
    }

    .steam-slide-image.is-swapping {
        opacity: .18;
        transform: scale(1.025);
    }

    .steam-slide-info {
        min-width: 0;
        height: 100%;
        padding: 18px;
        display: flex;
        flex-direction: column;
        background: linear-gradient(135deg, #101923 0%, #162333 52%, #071018 100%);
    }

    .steam-thumbs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .steam-thumb-button {
        overflow: hidden;
        border: 2px solid transparent;
        border-radius: 3px;
        background: rgba(255,255,255,.06);
        cursor: pointer;
    }

    .steam-thumb-button.is-active {
        border-color: #67e8f9;
    }

    .steam-thumbs img {
        width: 100%;
        height: 64px;
        object-fit: cover;
        border-radius: 2px;
        opacity: .82;
    }

    .steam-copy {
        margin-top: 18px;
    }

    .steam-game-title {
        display: block;
        color: #fff;
        font-size: 24px;
        font-weight: 800;
        line-height: 1.15;
    }

    .steam-game-title:hover {
        color: #67e8f9;
    }

    .steam-game-genres {
        margin-top: 8px;
        color: #a8b3c2;
        font-size: 13px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .steam-game-description {
        margin-top: 14px;
        color: #d4dde8;
        font-size: 14px;
        line-height: 1.45;
        max-height: 62px;
        overflow: hidden;
    }

    .steam-slide-bottom {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .steam-slide-bottom span {
        padding: 8px 10px;
        border-radius: 4px;
        background: rgba(34, 211, 238, .16);
        color: #a5f3fc;
        font-weight: 900;
    }

    .steam-slide-bottom strong {
        color: #fff;
        font-size: 15px;
        text-align: right;
    }

    .steam-arrow {
        position: absolute;
        top: 50%;
        z-index: 50;
        width: 48px;
        height: 78px;
        transform: translateY(-50%);
        border: 0;
        background: rgba(0, 0, 0, .58);
        color: #fff;
        font-size: 58px;
        line-height: 1;
        display: grid;
        place-items: center;
        cursor: pointer;
    }

    .steam-arrow:hover {
        background: rgba(14, 165, 233, .78);
    }

    .steam-arrow-left {
        left: 0;
    }

    .steam-arrow-right {
        right: 0;
    }

    .steam-dots {
        display: flex;
        justify-content: center;
        gap: 6px;
        margin-top: 12px;
    }

    .steam-dots button {
        width: 16px;
        height: 7px;
        border: 0;
        border-radius: 2px;
        background: rgba(255,255,255,.25);
        cursor: pointer;
    }

    .steam-dots button.is-active {
        background: #67e8f9;
    }

    @media (max-width: 760px) {
        .steam-carousel {
            width: 100%;
        }

        .steam-carousel-frame {
            height: 455px;
        }

        .steam-slide-grid {
            grid-template-columns: 1fr;
        }

        .steam-slide-image-link {
            height: 205px;
        }

        .steam-slide-info {
            min-height: 250px;
            padding: 14px;
        }

        .steam-thumbs {
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 6px;
        }

        .steam-thumbs img {
            height: 42px;
        }

        .steam-copy {
            margin-top: 12px;
        }

        .steam-game-title {
            font-size: 20px;
        }

        .steam-game-description {
            margin-top: 10px;
            max-height: 58px;
            font-size: 13px;
        }

        .steam-arrow {
            width: 38px;
            height: 58px;
            font-size: 42px;
        }

        .steam-dots {
            margin-top: 10px;
        }
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const carousel = document.getElementById('hero-carousel');
        if (!carousel) return;

        const slides = Array.from(carousel.querySelectorAll('.hero-slide'));
        const dots = Array.from(carousel.querySelectorAll('[data-carousel-dot]'));
        const prev = carousel.querySelector('[data-carousel-prev]');
        const next = carousel.querySelector('[data-carousel-next]');
        let active = 0;
        let timer = null;

        function show(index) {
            active = (index + slides.length) % slides.length;
            slides.forEach((slide, slideIndex) => {
                slide.classList.toggle('is-active', slideIndex === active);
                resetSlide(slide);
            });
            dots.forEach((dot, dotIndex) => {
                dot.classList.toggle('is-active', dotIndex === active);
            });
            const bg = document.getElementById('home-hero-bg');
            if (bg && slides[active]?.dataset.bg) bg.src = slides[active].dataset.bg;
        }

        function resetSlide(slide) {
            if (!slide) return;

            const mainImage = slide.querySelector('[data-main-slide-image]');
            if (mainImage && slide.dataset.defaultImage) {
                swapMainImage(mainImage, slide.dataset.defaultImage);
            }

            slide.dataset.lockedPreview = '';
            slide.querySelectorAll('.steam-thumb-button').forEach((item) => item.classList.remove('is-active'));
        }

        function swapMainImage(image, url) {
            if (!image || !url || image.src === url) return;

            image.classList.add('is-swapping');
            window.setTimeout(() => {
                image.src = url;
                image.addEventListener('load', () => {
                    image.classList.remove('is-swapping');
                }, { once: true });
            }, 120);
        }

        function previewThumb(thumb, shouldLock = false) {
            const slide = thumb.closest('.hero-slide');
            const mainImage = slide?.querySelector('[data-main-slide-image]');
            if (!slide || !mainImage) return;

            slide.querySelectorAll('.steam-thumb-button').forEach((item) => item.classList.remove('is-active'));
            thumb.classList.add('is-active');
            swapMainImage(mainImage, thumb.dataset.thumbUrl);

            if (shouldLock) {
                slide.dataset.lockedPreview = thumb.dataset.thumbUrl;
            }
        }

        carousel.querySelectorAll('.steam-thumb-button').forEach((thumb) => {
            thumb.addEventListener('mouseenter', () => previewThumb(thumb));
            thumb.addEventListener('mouseleave', () => {
                const slide = thumb.closest('.hero-slide');
                const mainImage = slide?.querySelector('[data-main-slide-image]');
                if (!slide || !mainImage) return;

                if (slide.dataset.lockedPreview) {
                    swapMainImage(mainImage, slide.dataset.lockedPreview);
                    slide.querySelectorAll('.steam-thumb-button').forEach((item) => {
                        item.classList.toggle('is-active', item.dataset.thumbUrl === slide.dataset.lockedPreview);
                    });
                    return;
                }

                resetSlide(slide);
            });
            thumb.addEventListener('click', () => previewThumb(thumb, true));
        });

        function start() {
            stop();
            timer = setInterval(() => show(active + 1), 7000);
        }

        function stop() {
            if (timer) clearInterval(timer);
        }

        prev?.addEventListener('click', () => { show(active - 1); start(); });
        next?.addEventListener('click', () => { show(active + 1); start(); });
        dots.forEach((dot) => dot.addEventListener('click', () => {
            show(Number(dot.dataset.carouselDot));
            start();
        }));
        carousel.addEventListener('mouseenter', stop);
        carousel.addEventListener('mouseleave', start);
        start();
    });
</script>
@endpush
@endsection

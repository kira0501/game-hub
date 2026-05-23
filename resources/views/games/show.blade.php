@extends('layouts.app')

@section('content')
@php
    $cover = $game->cover ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80';
    $approvedReviews = $game->reviews->where('status', 'approved');
    $positiveReviews = $approvedReviews->where('rating', '>=', 7)->count();
    $negativeReviews = $approvedReviews->where('rating', '<', 7)->count();
    $reviewTotal = $approvedReviews->count();
    $positivePercent = $reviewTotal ? round($positiveReviews / $reviewTotal * 100) : 0;
    $reviewLabel = $reviewTotal === 0 ? 'Нет отзывов' : ($positivePercent >= 80 ? 'В основном положительные' : ($positivePercent >= 55 ? 'Смешанные' : 'Отрицательные'));
    $recentReviews = $approvedReviews->where('created_at', '>=', now()->subDays(30));
    $recentTotal = $recentReviews->count();
    $recentPositive = $recentReviews->where('rating', '>=', 7)->count();
    $recentPercent = $recentTotal ? round($recentPositive / $recentTotal * 100) : $positivePercent;
    $mediaItems = $game->media->values();
    $displayCover = $mediaItems->firstWhere('type', 'image')?->url ?: $cover;
    $mediaImage = fn ($item) => $item->media->firstWhere('type', 'image')?->url
        ?: $item->cover
        ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=1200&q=80';
    $cardCover = fn ($item) => $item->cover
        ?: $item->media->firstWhere('type', 'image')?->url
        ?: 'https://images.unsplash.com/photo-1542751371-adc38448a05e?auto=format&fit=crop&w=900&q=80';
    $featureLabels = [
        'single_player' => ['icon' => '♟', 'label' => 'Для одного игрока'],
        'pvp_online' => ['icon' => '⚔', 'label' => 'Игрок против игрока (по сети)'],
        'pvp_splitscreen' => ['icon' => '⚔', 'label' => 'Игрок против игрока (общий/разделённый экран)'],
        'coop_online' => ['icon' => '☷', 'label' => 'Кооператив (по сети)'],
        'coop_splitscreen' => ['icon' => '☷', 'label' => 'Кооператив (общий/разделённый экран)'],
        'shared_splitscreen' => ['icon' => '▣', 'label' => 'Общий/разделённый экран'],
        'achievements' => ['icon' => '✹', 'label' => 'Достижения'],
        'in_app_purchases' => ['icon' => '▿', 'label' => 'Внутриигровые покупки'],
        'cloud' => ['icon' => '☁', 'label' => 'Cloud-сохранения'],
        'remote_play_together' => ['icon' => '☍', 'label' => 'Remote Play Together'],
        'family_sharing' => ['icon' => '♚', 'label' => 'Семейный доступ'],
        'accessibility' => ['icon' => '◌', 'label' => 'Функции доступности'],
    ];
    $controllerLabels = [
        'none' => 'Нет поддержки контроллера',
        'partial' => 'Частичная поддержка контроллера',
        'full' => 'Полная поддержка контроллера',
    ];
@endphp

<section class="relative border-b border-white/10">
    <div class="absolute inset-0">
        <img src="{{ $displayCover }}" class="h-full w-full object-cover opacity-25" alt="">
        <div class="absolute inset-0 bg-gradient-to-t from-hub-bg via-hub-bg/90 to-hub-bg/50"></div>
    </div>
    <div class="hub-container relative py-6 md:py-8">
        <p class="text-sm text-slate-400">Все игры › {{ $game->genres->first()?->name ?? 'Каталог' }}</p>
        <div class="mt-2 grid gap-4 lg:flex lg:items-center lg:justify-between">
            <h1 class="break-words text-3xl font-black text-white md:text-5xl">{{ $game->title }}</h1>
            <div class="grid gap-3 sm:flex sm:flex-wrap lg:justify-end">
                @auth
                    @if($isFavorite)
                        <div class="group relative flex min-w-0">
                            <button type="button" class="inline-flex items-center gap-2 rounded-l-md border border-cyan-300/30 bg-cyan-300/20 px-4 py-2 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-300/25">
                                <span class="text-lg leading-none">✓</span>
                                <span>В избранном</span>
                            </button>
                            <button type="button" class="rounded-r-md border border-l-0 border-cyan-300/30 bg-cyan-300/20 px-3 py-2 text-cyan-100 transition hover:bg-cyan-300/25">▾</button>
                            <div class="invisible absolute left-0 top-full z-30 mt-2 w-64 translate-y-1 rounded-md border border-white/10 bg-slate-200 p-3 text-sm text-slate-800 opacity-0 shadow-2xl shadow-black/40 transition group-hover:visible group-hover:translate-y-0 group-hover:opacity-100 group-focus-within:visible group-focus-within:translate-y-0 group-focus-within:opacity-100">
                                <div class="absolute -top-2 left-7 h-4 w-4 rotate-45 bg-slate-200"></div>
                                <p class="relative">Игра уже добавлена в избранное.</p>
                                <form method="POST" action="{{ route('favorites.toggle', $game) }}" class="relative mt-3">
                                    @csrf
                                    <button class="w-full rounded bg-slate-900 px-3 py-2 text-left font-semibold text-cyan-100 hover:bg-slate-800">Убрать из избранного</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('favorites.toggle', $game) }}">@csrf<button class="hub-btn w-full sm:w-auto">В избранное</button></form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="hub-btn">Войти, чтобы добавить</a>
                @endauth
                <a href="#prices" class="hub-btn-secondary">Где купить</a>
                @guest
                    <a href="{{ route('login') }}" class="hub-btn-secondary">Проверить ПК</a>
                @else
                    @if($pcConfigs->count())
                        <button type="button" id="quick-pc-open" class="hub-btn-secondary" data-single-config="{{ $pcConfigs->count() === 1 ? $pcConfigs->first()->id : '' }}">Проверить ПК</button>
                    @else
                        <a href="{{ route('pc.index') }}" class="hub-btn-secondary">Проверить ПК</a>
                    @endif
                @endguest
            </div>
        </div>
    </div>
</section>

<section class="hub-container grid gap-6 py-6 md:py-8 lg:grid-cols-[minmax(0,1fr)_320px]">
    <div class="min-w-0 space-y-6 md:space-y-8">
        <div class="hub-panel p-3 md:p-4">
            <div id="game-media-viewer" class="space-y-3">
                <div class="overflow-hidden rounded bg-black">
                    @php
                        $firstMedia = $mediaItems->first();
                    @endphp
                    @if($firstMedia?->type === 'video')
                        <iframe id="media-main-video" class="aspect-video w-full {{ \Illuminate\Support\Str::contains($firstMedia->url, ['.mp4', '.webm', '.m3u8']) ? 'hidden' : '' }}" src="{{ \Illuminate\Support\Str::contains($firstMedia->url, ['.mp4', '.webm', '.m3u8']) ? '' : $firstMedia->url }}" allowfullscreen></iframe>
                        <video id="media-main-file" class="{{ \Illuminate\Support\Str::contains($firstMedia->url, ['.mp4', '.webm', '.m3u8']) ? '' : 'hidden' }} aspect-video w-full bg-black" src="{{ \Illuminate\Support\Str::contains($firstMedia->url, ['.mp4', '.webm', '.m3u8']) ? $firstMedia->url : '' }}" controls playsinline></video>
                        <img id="media-main-image" class="hidden aspect-video w-full object-cover" src="" alt="">
                    @else
                        <iframe id="media-main-video" class="hidden aspect-video w-full" src="" allowfullscreen></iframe>
                        <video id="media-main-file" class="hidden aspect-video w-full bg-black" src="" controls playsinline></video>
                        <img id="media-main-image" class="aspect-video w-full object-cover" src="{{ $firstMedia?->url ?? $cover }}" alt="{{ $game->title }}">
                    @endif
                </div>
                <div class="flex max-w-full gap-2 overflow-x-auto pb-2">
                    @forelse($mediaItems as $index => $media)
                        <button type="button" class="media-thumb {{ $index === 0 ? 'ring-2 ring-cyan-300' : '' }} h-16 min-w-28 overflow-hidden rounded bg-slate-900 sm:h-20 sm:min-w-36" data-type="{{ $media->type }}" data-url="{{ $media->url }}" data-file-video="{{ $media->type === 'video' && \Illuminate\Support\Str::contains($media->url, ['.mp4', '.webm', '.m3u8']) ? '1' : '0' }}">
                            @if($media->type === 'video')
                                <div class="grid h-full w-full place-items-center bg-black/70 text-sm font-bold text-cyan-200">▶ Трейлер</div>
                            @else
                                <img src="{{ $media->url }}" alt="" class="h-full w-full object-cover">
                            @endif
                        </button>
                    @empty
                        <button type="button" class="media-thumb ring-2 ring-cyan-300 h-16 min-w-28 overflow-hidden rounded bg-slate-900 sm:h-20 sm:min-w-36" data-type="image" data-url="{{ $cover }}">
                            <img src="{{ $cover }}" alt="" class="h-full w-full object-cover">
                        </button>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="hub-panel p-5">
            <h2 class="mb-4 text-2xl font-black text-white">Описание</h2>
            <p class="leading-7 text-slate-300">{{ $game->description }}</p>
        </div>

        <div class="hub-panel p-5">
            <h2 class="mb-4 text-2xl font-black text-white">Системные требования</h2>
            @if($game->systemRequirement)
                <div class="grid gap-4 md:grid-cols-2 text-sm">
                    <div class="rounded-lg bg-white/5 p-4">
                        <h3 class="mb-3 font-bold text-cyan-200">Минимальные</h3>
                        <p>CPU: {{ $game->systemRequirement->cpu_min }}</p>
                        <p>GPU: {{ $game->systemRequirement->gpu_min }}</p>
                        <p>RAM: {{ $game->systemRequirement->ram_min }} GB</p>
                        <p>Storage: {{ $game->systemRequirement->storage_min }} GB</p>
                        <p>OS: {{ $game->systemRequirement->os_min }}</p>
                    </div>
                    <div class="rounded-lg bg-white/5 p-4">
                        <h3 class="mb-3 font-bold text-cyan-200">Рекомендуемые</h3>
                        <p>CPU: {{ $game->systemRequirement->cpu_rec }}</p>
                        <p>GPU: {{ $game->systemRequirement->gpu_rec }}</p>
                        <p>RAM: {{ $game->systemRequirement->ram_rec }} GB</p>
                        <p>Storage: {{ $game->systemRequirement->storage_rec }} GB</p>
                        <p>OS: {{ $game->systemRequirement->os_rec }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="hub-panel p-5">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-2xl font-black text-white">Отзывы</h2>
                <span class="text-sm text-slate-400">{{ $reviewLabel }} · {{ $positivePercent }}% положительных</span>
            </div>

            <div class="mb-6 rounded-lg border border-white/10 bg-white/5 p-4">
                <h3 class="text-lg font-black text-white">Оставить свой отзыв</h3>
                @auth
                    <p class="mt-2 text-sm text-slate-400">Отзыв появится на странице после проверки администратором.</p>
                    <form method="POST" action="{{ route('reviews.store', $game) }}" class="mt-4 grid gap-3">@csrf
                        <input name="rating" type="number" min="1" max="10" class="hub-input" placeholder="Оценка 1-10">
                        <textarea name="text" class="hub-input min-h-28" placeholder="Ваш отзыв"></textarea>
                        <button class="hub-btn w-fit">Отправить на модерацию</button>
                    </form>
                @else
                    <p class="mt-2 text-sm text-slate-400">Чтобы оставить отзыв, войдите в аккаунт или зарегистрируйтесь.</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="hub-btn">Войти</a>
                        <a href="{{ route('register') }}" class="hub-btn-secondary">Регистрация</a>
                    </div>
                @endauth
            </div>

            <div class="space-y-4">
                @forelse($approvedReviews as $review)
                    <div class="rounded-lg bg-white/5 p-4">
                        <div class="flex justify-between gap-4"><b>{{ $review->user->name }}</b><span class="text-cyan-200">{{ $review->rating }}/10</span></div>
                        <p class="mt-2 text-slate-300">{{ $review->text }}</p>
                    </div>
                @empty
                    <p class="text-slate-400">Одобренных отзывов пока нет.</p>
                @endforelse
            </div>
        </div>

        <div class="hub-panel p-5">
            <div class="mb-4 flex items-center justify-between gap-3">
                <h2 class="text-2xl font-black text-white">Похожие игры</h2>
                <a href="{{ route('games.index') }}" class="text-sm text-cyan-300 hover:text-white">Просмотреть все</a>
            </div>
            <div class="relative" id="similar-carousel">
                <button type="button" class="similar-arrow similar-prev">‹</button>
                <div class="similar-track flex max-w-full gap-3 overflow-x-auto scroll-smooth pb-5">
                    @foreach($similar as $item)
                        @php
                            $best = $item->prices->where('is_available', true)->whereNotNull('price')->sortBy('price')->first();
                        @endphp
                        <a href="{{ route('games.show', $item->slug) }}" class="min-w-[170px] overflow-hidden rounded bg-black/30 transition hover:-translate-y-1 sm:min-w-[220px]">
                            <img src="{{ $cardCover($item) }}" alt="{{ $item->title }}" class="h-24 w-full object-cover object-top sm:h-28">
                            <div class="flex items-center justify-between gap-3 p-3">
                                <span class="line-clamp-1 text-sm font-bold text-white">{{ $item->title }}</span>
                                <span class="whitespace-nowrap text-sm font-bold text-cyan-200">{{ $best ? number_format((float)$best->price, 0, '.', ' ') . '₽' : 'Нет' }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
                <button type="button" class="similar-arrow similar-next">›</button>
                <div class="mt-1 h-1 w-32 rounded bg-cyan-300/60"></div>
            </div>
        </div>
    </div>

    <aside class="min-w-0 space-y-6">
        <div class="hub-panel overflow-hidden">
            <img src="{{ $displayCover }}" class="aspect-video w-full object-cover" alt="{{ $game->title }}">
            <div class="space-y-4 p-5">
                <div class="grid grid-cols-[105px_1fr] gap-x-2 gap-y-2 text-sm sm:grid-cols-[130px_1fr]">
                    <span class="text-slate-500">Недавние обзоры:</span>
                    <span><b class="text-cyan-200">{{ $recentTotal ? $recentPercent.'% положительные' : $reviewLabel }}</b> <span class="text-slate-500">({{ $recentTotal }})</span></span>
                    <span class="text-slate-500">Обзоры:</span>
                    <span><b class="text-cyan-200">{{ $reviewLabel }}</b> <span class="text-slate-500">({{ $reviewTotal }})</span></span>
                    <span class="text-slate-500">Дата выхода:</span>
                    <span>{{ optional($game->release_date)->translatedFormat('j M Y г.') }}</span>
                    <span class="text-slate-500">Разработчик:</span>
                    <span class="text-cyan-200">{{ $game->developer }}</span>
                    <span class="text-slate-500">Издатель:</span>
                    <span class="text-cyan-200">{{ $game->publisher }}</span>
                </div>
                <div>
                    <p class="mb-2 text-xs uppercase text-slate-500">Популярные метки для этого продукта:</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($game->genres as $genre)
                            <a href="{{ route('genres.show', $genre->slug) }}" class="rounded bg-cyan-400/15 px-2 py-1 text-xs text-cyan-200">{{ $genre->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="hub-panel p-4">
            <div class="grid gap-1">
                @foreach($featureLabels as $key => $feature)
                    @if($game->hasFeature($key))
                        <div class="grid grid-cols-[34px_1fr] items-center rounded bg-white/5 text-sm">
                            <span class="grid h-9 place-items-center text-white">{{ $feature['icon'] }}</span>
                            <span class="text-cyan-300">{{ $feature['label'] }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="mt-4 text-sm">
                <p class="mb-2 text-slate-400">{{ $controllerLabels[$game->controller_support ?? 'partial'] ?? 'Частичная поддержка контроллера' }}</p>
                @if($game->supports_xbox_controller)
                    <div class="grid grid-cols-[34px_1fr] items-center rounded bg-white/5"><span class="grid h-9 place-items-center">🎮</span><span class="text-lime-300">Ваш контроллер Xbox</span></div>
                @endif
                @if($game->supports_playstation_controller)
                    <div class="mt-1 grid grid-cols-[34px_1fr] items-center rounded bg-white/5"><span class="grid h-9 place-items-center">🎮</span><span class="text-lime-300">Ваш контроллер PlayStation</span></div>
                @endif
                @if($game->developer_recommends_controller)
                    <div class="mt-3 grid grid-cols-[34px_1fr] items-center text-slate-200"><span class="grid h-9 place-items-center text-white">★</span><span>Разработчики рекомендуют играть с контроллером.</span></div>
                @endif
            </div>
        </div>

        <div id="prices" class="hub-panel p-5">
            <h2 class="mb-4 text-2xl font-black text-white">Где купить</h2>
            <div class="space-y-3">
                @foreach($priceComparison['rows'] as $row)
                    <div class="rounded-lg bg-white/5 p-4">
                        <div class="flex items-center justify-between"><b>{{ $row['store'] }}</b>@if($row['is_best'])<span class="text-xs text-cyan-200">Дешевле</span>@endif</div>
                        <p class="mt-2 text-slate-300">{{ $row['is_available'] ? ((float) $row['price'] === 0.0 ? 'Бесплатно' : number_format((float)$row['price'],0,'.',' ') . ' ' . $row['currency']) : 'Недоступно' }}</p>
                        @if($row['external_url'])<a class="mt-2 inline-block text-sm text-cyan-300" href="{{ $row['external_url'] }}" target="_blank">Купить</a>@endif
                    </div>
                @endforeach
            </div>
        </div>
    </aside>
</section>

@auth
    @if($pcConfigs->count())
        <div id="quick-pc-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-3 backdrop-blur-sm sm:p-4">
            <div class="max-h-[92vh] w-full max-w-2xl overflow-y-auto rounded-xl border border-white/10 bg-hub-panel shadow-2xl shadow-black/60">
                <div class="flex items-center justify-between border-b border-white/10 p-5">
                    <div>
                        <h2 class="text-2xl font-black text-white">Проверка ПК</h2>
                        <p class="mt-1 text-sm text-slate-400">{{ $game->title }}</p>
                    </div>
                    <button type="button" id="quick-pc-close" class="rounded-md border border-white/10 px-3 py-2 text-slate-300 hover:bg-white/10">Закрыть</button>
                </div>
                <div class="space-y-5 p-5">
                    @if($pcConfigs->count() > 1)
                        <div id="quick-pc-configs">
                            <p class="mb-3 text-sm text-slate-400">Выберите конфигурацию для проверки:</p>
                            <div class="grid gap-3 md:grid-cols-2">
                                @foreach($pcConfigs as $config)
                                    <button type="button" class="quick-pc-config rounded-lg border border-white/10 bg-white/5 p-4 text-left transition hover:border-cyan-300/50 hover:bg-cyan-300/10" data-config-id="{{ $config->id }}">
                                        <b class="text-white">{{ $config->title }}</b>
                                        <span class="mt-1 block text-sm text-slate-400">{{ $config->cpu }} / {{ $config->gpu }} / {{ $config->ram }} GB RAM</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div id="quick-pc-loading" class="hidden rounded-lg border border-white/10 bg-white/5 p-4 text-slate-300">Проверяю конфигурацию...</div>
                    <div id="quick-pc-result" class="hidden rounded-lg border border-white/10 bg-white/5 p-4"></div>
                    <a href="{{ route('pc.index') }}" class="inline-flex text-sm font-semibold text-cyan-300 hover:text-white">Создать новую конфигурацию</a>
                </div>
            </div>
        </div>
    @endif
@endauth

@push('scripts')
<style>
    .similar-track::-webkit-scrollbar {
        height: 8px;
    }

    .similar-track::-webkit-scrollbar-thumb {
        background: rgba(103, 232, 249, .55);
        border-radius: 999px;
    }

    #game-media-viewer .overflow-x-auto {
        scrollbar-color: rgba(103, 232, 249, .65) rgba(15, 23, 42, .95);
        scrollbar-width: thin;
    }

    #game-media-viewer .overflow-x-auto::-webkit-scrollbar {
        height: 10px;
    }

    #game-media-viewer .overflow-x-auto::-webkit-scrollbar-track {
        border-radius: 999px;
        background: rgba(15, 23, 42, .95);
    }

    #game-media-viewer .overflow-x-auto::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: rgba(103, 232, 249, .65);
    }

    .similar-arrow {
        position: absolute;
        top: 42%;
        z-index: 10;
        height: 58px;
        width: 38px;
        transform: translateY(-50%);
        background: rgba(0,0,0,.55);
        color: #fff;
        font-size: 42px;
        line-height: 1;
    }

    .similar-prev { left: -10px; }
    .similar-next { right: -10px; }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const video = document.getElementById('media-main-video');
        const fileVideo = document.getElementById('media-main-file');
        const image = document.getElementById('media-main-image');
        const thumbs = Array.from(document.querySelectorAll('.media-thumb'));

        thumbs.forEach((thumb) => {
            thumb.addEventListener('click', () => {
                thumbs.forEach((item) => item.classList.remove('ring-2', 'ring-cyan-300'));
                thumb.classList.add('ring-2', 'ring-cyan-300');

                if (thumb.dataset.type === 'video') {
                    image.classList.add('hidden');
                    video.src = '';
                    video.classList.add('hidden');
                    fileVideo.pause();
                    fileVideo.src = '';
                    fileVideo.classList.add('hidden');

                    if (thumb.dataset.fileVideo === '1') {
                        fileVideo.classList.remove('hidden');
                        fileVideo.src = thumb.dataset.url;
                    } else {
                        video.classList.remove('hidden');
                        video.src = thumb.dataset.url;
                    }
                } else {
                    video.classList.add('hidden');
                    video.src = '';
                    fileVideo.pause();
                    fileVideo.classList.add('hidden');
                    fileVideo.src = '';
                    image.classList.remove('hidden');
                    image.src = thumb.dataset.url;
                }
            });
        });

        const carousel = document.getElementById('similar-carousel');
        const track = carousel?.querySelector('.similar-track');
        carousel?.querySelector('.similar-prev')?.addEventListener('click', () => track.scrollBy({ left: -460, behavior: 'smooth' }));
        carousel?.querySelector('.similar-next')?.addEventListener('click', () => track.scrollBy({ left: 460, behavior: 'smooth' }));

        const pcOpen = document.getElementById('quick-pc-open');
        const pcModal = document.getElementById('quick-pc-modal');
        const pcClose = document.getElementById('quick-pc-close');
        const pcLoading = document.getElementById('quick-pc-loading');
        const pcResult = document.getElementById('quick-pc-result');
        const pcConfigs = document.getElementById('quick-pc-configs');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        const statusClasses = {
            recommended: 'border-cyan-300/30 bg-cyan-400/10 text-cyan-100',
            min: 'border-amber-300/30 bg-amber-400/10 text-amber-100',
            not_supported: 'border-red-300/30 bg-red-400/10 text-red-100',
        };

        function openPcModal() {
            pcModal?.classList.remove('hidden');
            pcModal?.classList.add('flex');
        }

        function closePcModal() {
            pcModal?.classList.add('hidden');
            pcModal?.classList.remove('flex');
        }

        async function runPcCheck(configId) {
            if (!configId) return;

            openPcModal();
            pcConfigs?.classList.add('hidden');
            pcResult?.classList.add('hidden');
            pcLoading?.classList.remove('hidden');

            const url = @json(route('games.pc-check', ['game' => $game->id, 'pcConfig' => '__CONFIG__'])).replace('__CONFIG__', configId);
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
            });
            const data = await response.json();

            pcLoading?.classList.add('hidden');
            pcResult.className = `rounded-lg border p-4 ${statusClasses[data.level] || statusClasses.not_supported}`;
            pcResult.innerHTML = `
                <h3 class="text-xl font-black">${data.title}</h3>
                <p class="mt-2">${data.conclusion}</p>
                <div class="mt-4 grid gap-2 md:grid-cols-2">
                    ${Object.values(data.details || {}).map((item) => `
                        <div class="rounded bg-black/20 p-3 text-sm">
                            <div class="flex justify-between gap-3"><b>${item.label}</b><span>${item.value}</span></div>
                            <p class="mt-1 opacity-80">Мин: ${item.minimum}</p>
                            <p class="opacity-80">Рек: ${item.recommended}</p>
                        </div>
                    `).join('')}
                </div>
            `;
            pcResult.classList.remove('hidden');
        }

        pcOpen?.addEventListener('click', () => {
            const singleConfig = pcOpen.dataset.singleConfig;
            if (singleConfig) {
                runPcCheck(singleConfig);
                return;
            }

            pcResult?.classList.add('hidden');
            pcLoading?.classList.add('hidden');
            pcConfigs?.classList.remove('hidden');
            openPcModal();
        });

        document.querySelectorAll('.quick-pc-config').forEach((button) => {
            button.addEventListener('click', () => runPcCheck(button.dataset.configId));
        });

        pcClose?.addEventListener('click', closePcModal);
        pcModal?.addEventListener('click', (event) => {
            if (event.target === pcModal) closePcModal();
        });
    });
</script>
@endpush
@endsection

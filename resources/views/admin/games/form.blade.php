@extends('layouts.admin')

@section('content')
@php
    $req = $game->systemRequirement;
    $sectionClass = 'rounded-lg border border-white/10 bg-white/5 p-5';
    $hintClass = 'mt-1 text-xs text-slate-500';
@endphp

<div class="max-w-6xl">
    <a href="{{ route('admin.games.index') }}" class="text-sm text-cyan-300 hover:text-white">← Назад к играм</a>
    <h1 class="mt-3 text-3xl font-black">{{ $game->exists ? 'Редактировать игру' : 'Создать игру' }}</h1>
    <p class="mt-2 text-sm text-slate-400">Поля с URL оставлены для внешних изображений, загрузка файлов сохраняет медиа в Laravel Storage.</p>

    <form method="POST" action="{{ $game->exists ? route('admin.games.update',$game) : route('admin.games.store') }}" enctype="multipart/form-data" class="mt-6 grid gap-6">
        @csrf
        @if($game->exists) @method('PUT') @endif

        <section class="{{ $sectionClass }}">
            <h2 class="text-xl font-black text-white">Основная информация</h2>
            <p class="{{ $hintClass }}">Название, slug, описание, студия и дата выхода используются в каталоге, карточках и странице игры.</p>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="grid gap-1"><span class="hub-label">Название</span><input name="title" value="{{ old('title',$game->title) }}" class="hub-input"></label>
                <label class="grid gap-1"><span class="hub-label">Slug</span><input name="slug" value="{{ old('slug',$game->slug) }}" class="hub-input" placeholder="auto если пусто"></label>
                <label class="grid gap-1"><span class="hub-label">Разработчик</span><input name="developer" value="{{ old('developer',$game->developer) }}" class="hub-input"></label>
                <label class="grid gap-1"><span class="hub-label">Издатель</span><input name="publisher" value="{{ old('publisher',$game->publisher) }}" class="hub-input"></label>
                <label class="grid gap-1"><span class="hub-label">Дата выхода</span><input name="release_date" type="date" value="{{ old('release_date', optional($game->release_date)->format('Y-m-d')) }}" class="hub-input"></label>
                <label class="grid gap-1"><span class="hub-label">Metacritic</span><input name="metacritic_score" type="number" value="{{ old('metacritic_score',$game->metacritic_score) }}" class="hub-input"></label>
                <label class="grid gap-1 md:col-span-2"><span class="hub-label">Описание</span><textarea name="description" class="hub-input min-h-36">{{ old('description',$game->description) }}</textarea></label>
                <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$game->is_active))> Активна на сайте</label>
            </div>
        </section>

        <section class="{{ $sectionClass }}">
            <h2 class="text-xl font-black text-white">Медиа</h2>
            <p class="{{ $hintClass }}">Обложка — вертикальная карточка. Главное изображение — страница игры. Карусель — широкий слайд на главной.</p>
            <div class="mt-4 grid gap-4 md:grid-cols-3">
                <label class="grid gap-1"><span class="hub-label">Обложка игры URL</span><input name="cover" value="{{ old('cover',$game->cover) }}" class="hub-input"></label>
                <label class="grid gap-1"><span class="hub-label">Главное изображение URL</span><input name="hero_image" value="{{ old('hero_image',$game->hero_image) }}" class="hub-input"></label>
                <label class="grid gap-1"><span class="hub-label">Изображение карусели URL</span><input name="carousel_image" value="{{ old('carousel_image',$game->carousel_image) }}" class="hub-input"></label>
                <label class="grid gap-1"><span class="hub-label">Загрузить обложку</span><input name="cover_file" type="file" accept="image/*" class="hub-input"></label>
                <label class="grid gap-1"><span class="hub-label">Загрузить главное изображение</span><input name="hero_image_file" type="file" accept="image/*" class="hub-input"></label>
                <label class="grid gap-1"><span class="hub-label">Загрузить изображение карусели</span><input name="carousel_image_file" type="file" accept="image/*" class="hub-input"></label>
                <label class="grid gap-1 md:col-span-2"><span class="hub-label">Rutube embed-код или ссылка трейлера</span><input name="trailer_url" value="{{ old('trailer_url',$game->trailer_url) }}" class="hub-input" placeholder="https://rutube.ru/play/embed/... или iframe-код"></label>
                <label class="grid gap-1"><span class="hub-label">Загрузить видео</span><input name="video_files[]" type="file" accept="video/mp4,video/webm,video/ogg" multiple class="hub-input"></label>
                <label class="grid gap-1 md:col-span-3"><span class="hub-label">Загрузить изображения галереи</span><input name="gallery_images[]" type="file" accept="image/*" multiple class="hub-input"></label>
                <label class="grid gap-1 md:col-span-3"><span class="hub-label">URL галереи, каждый с новой строки</span><textarea name="gallery_urls" class="hub-input min-h-24" placeholder="https://...jpg&#10;https://...webp"></textarea></label>
            </div>
            @if($media->count())
                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($media as $item)
                        <label class="block overflow-hidden rounded border border-white/10 bg-black/20">
                            <div class="h-28 bg-slate-950">
                                @if($item->type === 'image')
                                    <img src="{{ $item->url }}" class="h-full w-full object-cover" alt="">
                                @else
                                    <div class="grid h-full place-items-center text-cyan-200">Видео</div>
                                @endif
                            </div>
                            <span class="flex items-center gap-2 p-3 text-sm"><input type="checkbox" name="remove_media[]" value="{{ $item->id }}"> Удалить</span>
                        </label>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="{{ $sectionClass }}">
            <h2 class="text-xl font-black text-white">Категории и жанры</h2>
            <div class="mt-4 grid gap-2 md:grid-cols-4">
                @foreach($genres as $genre)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="genres[]" value="{{ $genre->id }}" @checked(in_array($genre->id, old('genres', $game->genres->pluck('id')->all())))> {{ $genre->name }}</label>
                @endforeach
            </div>
        </section>

        <section class="{{ $sectionClass }}">
            <h2 class="text-xl font-black text-white">Цены и рейтинг</h2>
            <p class="{{ $hintClass }}">Цены Steam/Epic удобнее редактировать в разделе “Цены”, здесь только пользовательская оценка.</p>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="grid gap-1"><span class="hub-label">User score</span><input name="user_score_avg" type="number" step="0.1" value="{{ old('user_score_avg',$game->user_score_avg) }}" class="hub-input"></label>
            </div>
        </section>

        <section class="{{ $sectionClass }}">
            <h2 class="text-xl font-black text-white">Системные требования</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @foreach(['cpu_min'=>'CPU минимум','cpu_rec'=>'CPU рекомендуется','gpu_min'=>'GPU минимум','gpu_rec'=>'GPU рекомендуется','ram_min'=>'RAM минимум','ram_rec'=>'RAM рекомендуется','storage_min'=>'Место минимум','storage_rec'=>'Место рекомендуется','os_min'=>'OS минимум','os_rec'=>'OS рекомендуется','directx_min'=>'DirectX минимум','directx_rec'=>'DirectX рекомендуется'] as $field=>$label)
                    <label class="grid gap-1"><span class="hub-label">{{ $label }}</span><input name="{{ $field }}" value="{{ old($field, $req?->{$field}) }}" class="hub-input"></label>
                @endforeach
            </div>
        </section>

        <section class="{{ $sectionClass }}">
            <h2 class="text-xl font-black text-white">Игровые функции</h2>
            @php
                $featureOptions = [
                    'single_player' => 'Для одного игрока',
                    'pvp_online' => 'PvP по сети',
                    'pvp_splitscreen' => 'PvP общий экран',
                    'coop_online' => 'Кооператив по сети',
                    'coop_splitscreen' => 'Кооператив общий экран',
                    'shared_splitscreen' => 'Разделённый экран',
                    'achievements' => 'Достижения',
                    'in_app_purchases' => 'Внутриигровые покупки',
                    'cloud' => 'Cloud',
                    'remote_play_together' => 'Remote Play Together',
                    'family_sharing' => 'Семейный доступ',
                    'accessibility' => 'Доступность',
                ];
                $selectedFeatures = old('play_features', $game->play_features ?? ['single_player']);
            @endphp
            <div class="mt-4 grid gap-2 md:grid-cols-3">
                @foreach($featureOptions as $value => $label)
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="play_features[]" value="{{ $value }}" @checked(in_array($value, $selectedFeatures, true))> {{ $label }}</label>
                @endforeach
            </div>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <select name="controller_support" class="hub-select">
                    <option value="none" @selected(old('controller_support', $game->controller_support ?? 'partial') === 'none')>Нет поддержки контроллера</option>
                    <option value="partial" @selected(old('controller_support', $game->controller_support ?? 'partial') === 'partial')>Частичная поддержка</option>
                    <option value="full" @selected(old('controller_support', $game->controller_support ?? 'partial') === 'full')>Полная поддержка</option>
                </select>
                <div class="grid gap-2 text-sm">
                    <label class="flex items-center gap-2"><input type="checkbox" name="supports_xbox_controller" value="1" @checked(old('supports_xbox_controller', $game->supports_xbox_controller))> Xbox controller</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="supports_playstation_controller" value="1" @checked(old('supports_playstation_controller', $game->supports_playstation_controller))> PlayStation controller</label>
                    <label class="flex items-center gap-2"><input type="checkbox" name="developer_recommends_controller" value="1" @checked(old('developer_recommends_controller', $game->developer_recommends_controller))> Разработчики рекомендуют контроллер</label>
                </div>
            </div>
        </section>

        @if($errors->any())
            <div class="rounded border border-red-400/30 bg-red-500/10 p-3 text-red-100">{{ $errors->first() }}</div>
        @endif

        <button class="hub-btn w-fit">Сохранить игру</button>
    </form>
</div>
@endsection

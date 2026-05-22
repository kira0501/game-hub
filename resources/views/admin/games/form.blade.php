@extends('layouts.admin')

@section('content')
@php($req = $game->systemRequirement)
<h1 class="text-3xl font-black">{{ $game->exists ? 'Редактировать игру' : 'Создать игру' }}</h1>
<form method="POST" action="{{ $game->exists ? route('admin.games.update',$game) : route('admin.games.store') }}" class="mt-6 grid gap-6">@csrf @if($game->exists) @method('PUT') @endif
    <div class="rounded-lg border border-white/10 bg-white/5 p-5 grid gap-4 md:grid-cols-2">
        <input name="title" value="{{ old('title',$game->title) }}" class="hub-input" placeholder="Название">
        <input name="slug" value="{{ old('slug',$game->slug) }}" class="hub-input" placeholder="slug">
        <input name="cover" value="{{ old('cover',$game->cover) }}" class="hub-input" placeholder="URL обложки">
        <input name="trailer_url" value="{{ old('trailer_url',$game->trailer_url) }}" class="hub-input" placeholder="Trailer embed URL">
        <input name="developer" value="{{ old('developer',$game->developer) }}" class="hub-input" placeholder="Разработчик">
        <input name="publisher" value="{{ old('publisher',$game->publisher) }}" class="hub-input" placeholder="Издатель">
        <input name="release_date" type="date" value="{{ old('release_date', optional($game->release_date)->format('Y-m-d')) }}" class="hub-input">
        <input name="metacritic_score" type="number" value="{{ old('metacritic_score',$game->metacritic_score) }}" class="hub-input" placeholder="Metacritic">
        <input name="user_score_avg" type="number" step="0.1" value="{{ old('user_score_avg',$game->user_score_avg) }}" class="hub-input" placeholder="User score">
        <label class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" @checked(old('is_active',$game->is_active))> Активна</label>
        <textarea name="description" class="hub-input md:col-span-2 min-h-32" placeholder="Описание">{{ old('description',$game->description) }}</textarea>
        <div class="md:col-span-2 grid gap-2 md:grid-cols-4">@foreach($genres as $genre)<label class="flex items-center gap-2 text-sm"><input type="checkbox" name="genres[]" value="{{ $genre->id }}" @checked(in_array($genre->id, old('genres', $game->genres->pluck('id')->all())))> {{ $genre->name }}</label>@endforeach</div>
    </div>
    <div class="rounded-lg border border-white/10 bg-white/5 p-5 grid gap-4 md:grid-cols-2">
        <h2 class="md:col-span-2 text-xl font-bold">Системные требования</h2>
        @foreach(['cpu_min'=>'CPU min','cpu_rec'=>'CPU rec','gpu_min'=>'GPU min','gpu_rec'=>'GPU rec','ram_min'=>'RAM min','ram_rec'=>'RAM rec','storage_min'=>'Storage min','storage_rec'=>'Storage rec','os_min'=>'OS min','os_rec'=>'OS rec','directx_min'=>'DirectX min','directx_rec'=>'DirectX rec'] as $field=>$label)
            <input name="{{ $field }}" value="{{ old($field, $req?->{$field}) }}" class="hub-input" placeholder="{{ $label }}">
        @endforeach
    </div>
    <div class="rounded-lg border border-white/10 bg-white/5 p-5 grid gap-4">
        <h2 class="text-xl font-bold">Игровые функции</h2>
        @php
            $featureOptions = [
                'single_player' => 'Для одного игрока',
                'pvp_online' => 'Игрок против игрока по сети',
                'pvp_splitscreen' => 'Игрок против игрока, общий экран',
                'coop_online' => 'Кооператив по сети',
                'coop_splitscreen' => 'Кооператив, общий экран',
                'shared_splitscreen' => 'Общий/разделённый экран',
                'achievements' => 'Достижения',
                'in_app_purchases' => 'Внутриигровые покупки',
                'cloud' => 'Cloud-сохранения',
                'remote_play_together' => 'Remote Play Together',
                'family_sharing' => 'Семейный доступ',
                'accessibility' => 'Функции доступности',
            ];
            $selectedFeatures = old('play_features', $game->play_features ?? ['single_player']);
        @endphp
        <div class="grid gap-2 md:grid-cols-3">
            @foreach($featureOptions as $value => $label)
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="play_features[]" value="{{ $value }}" @checked(in_array($value, $selectedFeatures, true))>
                    {{ $label }}
                </label>
            @endforeach
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <select name="controller_support" class="hub-select">
                <option value="none" @selected(old('controller_support', $game->controller_support ?? 'partial') === 'none')>Нет поддержки контроллера</option>
                <option value="partial" @selected(old('controller_support', $game->controller_support ?? 'partial') === 'partial')>Частичная поддержка</option>
                <option value="full" @selected(old('controller_support', $game->controller_support ?? 'partial') === 'full')>Полная поддержка</option>
            </select>
            <div class="grid gap-2 text-sm">
                <label class="flex items-center gap-2"><input type="checkbox" name="supports_xbox_controller" value="1" @checked(old('supports_xbox_controller', $game->supports_xbox_controller))> Поддержка Xbox controller</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="supports_playstation_controller" value="1" @checked(old('supports_playstation_controller', $game->supports_playstation_controller))> Поддержка PlayStation controller</label>
                <label class="flex items-center gap-2"><input type="checkbox" name="developer_recommends_controller" value="1" @checked(old('developer_recommends_controller', $game->developer_recommends_controller))> Разработчики рекомендуют контроллер</label>
            </div>
        </div>
    </div>
    @if($errors->any())<div class="text-red-300">{{ $errors->first() }}</div>@endif
    <button class="hub-btn w-fit">Сохранить</button>
</form>
@endsection

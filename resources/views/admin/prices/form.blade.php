@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-black">{{ $price->exists ? 'Редактировать цену' : 'Создать цену' }}</h1>
<form method="POST" action="{{ $price->exists ? route('admin.prices.update',$price) : route('admin.prices.store') }}" class="mt-6 max-w-2xl space-y-4 rounded-lg border border-white/10 bg-white/5 p-5">@csrf @if($price->exists) @method('PUT') @endif
    <select name="game_id" class="hub-select">@foreach($games as $game)<option value="{{ $game->id }}" @selected(old('game_id',$price->game_id)==$game->id)>{{ $game->title }}</option>@endforeach</select>
    <select name="store_id" class="hub-select">@foreach($stores as $store)<option value="{{ $store->id }}" @selected(old('store_id',$price->store_id)==$store->id)>{{ $store->name }}</option>@endforeach</select>
    <input name="price" type="number" step="0.01" value="{{ old('price',$price->price) }}" class="hub-input" placeholder="Цена"><input name="currency" value="{{ old('currency',$price->currency ?? 'RUB') }}" class="hub-input" placeholder="Валюта"><input name="external_url" value="{{ old('external_url',$price->external_url) }}" class="hub-input" placeholder="URL покупки">
    <label class="flex items-center gap-2"><input type="checkbox" name="is_available" value="1" @checked(old('is_available',$price->is_available))> Доступна</label>
    @if($errors->any())<div class="text-red-300">{{ $errors->first() }}</div>@endif
    <button class="hub-btn">Сохранить</button>
</form>
@endsection

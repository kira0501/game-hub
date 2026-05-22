@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-black">{{ $store->exists ? 'Редактировать магазин' : 'Создать магазин' }}</h1>
<form method="POST" action="{{ $store->exists ? route('admin.stores.update',$store) : route('admin.stores.store') }}" class="mt-6 max-w-xl space-y-4 rounded-lg border border-white/10 bg-white/5 p-5">@csrf @if($store->exists) @method('PUT') @endif
    <input name="name" value="{{ old('name',$store->name) }}" class="hub-input" placeholder="Название"><input name="slug" value="{{ old('slug',$store->slug) }}" class="hub-input" placeholder="slug"><input name="logo" value="{{ old('logo',$store->logo) }}" class="hub-input" placeholder="Logo URL"><input name="base_url" value="{{ old('base_url',$store->base_url) }}" class="hub-input" placeholder="Base URL">
    @if($errors->any())<div class="text-red-300">{{ $errors->first() }}</div>@endif
    <button class="hub-btn">Сохранить</button>
</form>
@endsection

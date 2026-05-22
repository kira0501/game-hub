@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-black">{{ $genre->exists ? 'Редактировать жанр' : 'Создать жанр' }}</h1>
<form method="POST" action="{{ $genre->exists ? route('admin.genres.update',$genre) : route('admin.genres.store') }}" class="mt-6 max-w-xl space-y-4 rounded-lg border border-white/10 bg-white/5 p-5">@csrf @if($genre->exists) @method('PUT') @endif
    <input name="name" value="{{ old('name',$genre->name) }}" class="hub-input" placeholder="Название">
    <input name="slug" value="{{ old('slug',$genre->slug) }}" class="hub-input" placeholder="slug">
    @if($errors->any())<div class="text-red-300">{{ $errors->first() }}</div>@endif
    <button class="hub-btn">Сохранить</button>
</form>
@endsection

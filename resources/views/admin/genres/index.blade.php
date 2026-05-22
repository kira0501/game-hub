@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between"><h1 class="text-3xl font-black">Жанры</h1><a class="hub-btn" href="{{ route('admin.genres.create') }}">Добавить</a></div>
<div class="mt-6 grid gap-3">@foreach($genres as $genre)<div class="rounded-lg border border-white/10 bg-white/5 p-4 flex justify-between"><span>{{ $genre->name }} <b class="text-slate-400">({{ $genre->games_count }})</b></span><span class="flex gap-3"><a class="text-cyan-300" href="{{ route('admin.genres.edit',$genre) }}">Ред.</a><form method="POST" action="{{ route('admin.genres.destroy',$genre) }}">@csrf @method('DELETE')<button class="text-red-300">Удал.</button></form></span></div>@endforeach</div>
<div class="mt-6">{{ $genres->links() }}</div>
@endsection

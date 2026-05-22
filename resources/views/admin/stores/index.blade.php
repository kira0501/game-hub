@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between"><h1 class="text-3xl font-black">Магазины</h1><a class="hub-btn" href="{{ route('admin.stores.create') }}">Добавить</a></div>
<div class="mt-6 grid gap-3">@foreach($stores as $store)<div class="rounded-lg border border-white/10 bg-white/5 p-4 flex justify-between"><span>{{ $store->name }} <b class="text-slate-400">({{ $store->prices_count }} цен)</b></span><span class="flex gap-3"><a class="text-cyan-300" href="{{ route('admin.stores.edit',$store) }}">Ред.</a><form method="POST" action="{{ route('admin.stores.destroy',$store) }}">@csrf @method('DELETE')<button class="text-red-300">Удал.</button></form></span></div>@endforeach</div>
<div class="mt-6">{{ $stores->links() }}</div>
@endsection

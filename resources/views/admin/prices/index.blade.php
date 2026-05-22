@extends('layouts.admin')

@section('content')
<div class="flex items-center justify-between"><h1 class="text-3xl font-black">Цены</h1><a class="hub-btn" href="{{ route('admin.prices.create') }}">Добавить</a></div>
<div class="mt-6 grid gap-3">@foreach($prices as $price)<div class="rounded-lg border border-white/10 bg-white/5 p-4 flex justify-between"><span>{{ $price->game->title }} — {{ $price->store->name }} <b class="text-cyan-300">{{ $price->is_available ? number_format((float)$price->price,0,'.',' ').' '.$price->currency : 'Недоступно' }}</b></span><span class="flex gap-3"><a class="text-cyan-300" href="{{ route('admin.prices.edit',$price) }}">Ред.</a><form method="POST" action="{{ route('admin.prices.destroy',$price) }}">@csrf @method('DELETE')<button class="text-red-300">Удал.</button></form></span></div>@endforeach</div>
<div class="mt-6">{{ $prices->links() }}</div>
@endsection

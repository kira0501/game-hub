@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-black">Отзывы</h1>
<div class="mt-6 grid gap-3">@foreach($reviews as $review)<div class="rounded-lg border border-white/10 bg-white/5 p-4"><div class="flex flex-wrap justify-between gap-3"><b>{{ $review->game->title }} — {{ $review->user->name }}</b><span>{{ $review->rating }}/10</span></div><p class="mt-2 text-slate-300">{{ $review->text }}</p><form method="POST" action="{{ route('admin.reviews.update',$review) }}" class="mt-3 flex gap-2">@csrf @method('PATCH')<select name="status" class="hub-select max-w-48"><option value="pending" @selected($review->status==='pending')>На модерации</option><option value="approved" @selected($review->status==='approved')>Одобрен</option><option value="rejected" @selected($review->status==='rejected')>Отклонён</option></select><button class="hub-btn-secondary">Сохранить</button></form></div>@endforeach</div>
<div class="mt-6">{{ $reviews->links() }}</div>
@endsection

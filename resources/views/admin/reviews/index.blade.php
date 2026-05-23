@extends('layouts.admin')

@section('content')
@php
    $tabs = [
        'pending' => 'На модерации',
        'approved' => 'Одобренные',
        'rejected' => 'Отклонённые',
        'all' => 'Все',
    ];
    $labels = [
        'pending' => 'На модерации',
        'approved' => 'Одобрен',
        'rejected' => 'Отклонён',
    ];
@endphp

<div class="flex flex-wrap items-end justify-between gap-4">
    <div>
        <h1 class="text-3xl font-black">Отзывы</h1>
        <p class="mt-2 text-sm text-slate-400">Проверяйте новые отзывы, одобряйте полезные и отклоняйте неподходящие.</p>
    </div>
</div>

<div class="mt-6 flex flex-wrap gap-2">
    @foreach($tabs as $value => $label)
        <a href="{{ route('admin.reviews.index', ['status' => $value]) }}" class="rounded-md border px-3 py-2 text-sm font-semibold transition {{ $status === $value ? 'border-cyan-300 bg-cyan-400/15 text-cyan-100' : 'border-white/10 bg-white/5 text-slate-300 hover:border-cyan-300/40' }}">
            {{ $label }}
            @if(isset($counts[$value]))
                <span class="ml-1 text-slate-400">{{ $counts[$value] }}</span>
            @endif
        </a>
    @endforeach
</div>

<div class="mt-6 grid gap-3">
    @forelse($reviews as $review)
        <div class="rounded-lg border border-white/10 bg-white/5 p-4">
            <div class="flex flex-wrap justify-between gap-3">
                <div>
                    <b>{{ $review->game->title }} — {{ $review->user->name }}</b>
                    <span class="mt-1 block text-xs text-slate-500">{{ $review->created_at->format('d.m.Y H:i') }}</span>
                </div>
                <span class="h-fit rounded bg-cyan-400/15 px-2 py-1 text-sm font-bold text-cyan-200">{{ $review->rating }}/10</span>
            </div>
            <p class="mt-3 text-slate-300">{{ $review->text }}</p>
            <div class="mt-4 flex flex-wrap gap-2">
                <span class="rounded bg-white/10 px-3 py-2 text-sm text-slate-300">{{ $labels[$review->status] ?? $review->status }}</span>
                <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="approved">
                    <button class="hub-btn-secondary">Одобрить</button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.update', $review) }}">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <button class="hub-btn-secondary">Отклонить</button>
                </form>
                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Удалить отзыв окончательно?');">
                    @csrf
                    @method('DELETE')
                    <button class="rounded-md border border-red-400/30 bg-red-500/10 px-3 py-2 text-sm font-semibold text-red-200 hover:bg-red-500/20">Удалить</button>
                </form>
            </div>
        </div>
    @empty
        <div class="rounded-lg border border-white/10 bg-white/5 p-6 text-slate-400">В этой очереди отзывов нет.</div>
    @endforelse
</div>

<div class="mt-6">{{ $reviews->links() }}</div>
@endsection

@extends('layouts.admin')

@section('content')
@php
    $roleLabels = ['admin' => 'Администратор', 'user' => 'Пользователь'];
    $statusLabels = ['active' => 'Активен', 'blocked' => 'Заблокирован'];
@endphp
<div class="flex items-center justify-between">
    <h1 class="text-3xl font-black">Пользователи</h1>
    <a class="hub-btn" href="{{ route('admin.users.create') }}">Добавить</a>
</div>

<div class="mt-6 grid gap-3">
    @forelse($users as $user)
        <div class="rounded-lg border border-white/10 bg-white/5 p-4">
            <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-bold">{{ $user->name }}</span>
                        @if(auth()->id() === $user->id)
                            <span class="rounded-md bg-cyan-400/15 px-2 py-1 text-xs font-bold text-cyan-200">Это вы</span>
                        @endif
                        <span @class([
                            'rounded-md px-2 py-1 text-xs font-bold',
                            'bg-cyan-400/15 text-cyan-200' => $user->status === 'active',
                            'bg-amber-400/15 text-amber-200' => $user->status === 'blocked',
                        ])>
                            {{ $statusLabels[$user->status] ?? $user->status }}
                        </span>
                    </div>
                    <div class="mt-1 text-sm text-slate-400">
                        {{ $user->email }} / {{ $roleLabels[$user->role] ?? $user->role }}
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a class="rounded-md bg-cyan-500 px-3 py-2 text-sm font-bold text-slate-950 transition hover:bg-cyan-300" href="{{ route('admin.users.edit', $user) }}">Редактировать</a>

                    <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                        @csrf
                        @method('PATCH')
                        <button class="rounded-md bg-amber-400 px-3 py-2 text-sm font-bold text-slate-950 transition hover:bg-amber-300 disabled:cursor-not-allowed disabled:opacity-45" @disabled(auth()->id() === $user->id)>
                            {{ $user->status === 'active' ? 'Заблокировать' : 'Разблокировать' }}
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Удалить пользователя {{ addslashes($user->name) }} окончательно? Лучше использовать блокировку, чтобы сохранить отзывы и связи.');">
                        @csrf
                        @method('DELETE')
                        <button class="rounded-md bg-red-500 px-3 py-2 text-sm font-bold text-white transition hover:bg-red-400 disabled:cursor-not-allowed disabled:opacity-45" @disabled(auth()->id() === $user->id)>Удалить</button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="rounded-lg border border-white/10 bg-white/5 p-6 text-center text-slate-400">Пользователи не найдены.</div>
    @endforelse
</div>
<div class="mt-6">{{ $users->links() }}</div>
@endsection

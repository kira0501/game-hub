@extends('layouts.admin')

@section('content')
@php
    $roleLabels = ['admin' => 'Администратор', 'user' => 'Пользователь'];
    $statusLabels = ['active' => 'Активен', 'blocked' => 'Заблокирован'];
@endphp
<div class="flex items-center justify-between"><h1 class="text-3xl font-black">Пользователи</h1><a class="hub-btn" href="{{ route('admin.users.create') }}">Добавить</a></div>
<div class="mt-6 grid gap-3">@foreach($users as $user)<div class="rounded-lg border border-white/10 bg-white/5 p-4 flex justify-between"><span>{{ $user->name }} <span class="text-slate-400">{{ $user->email }} / {{ $roleLabels[$user->role] ?? $user->role }} / {{ $statusLabels[$user->status] ?? $user->status }}</span></span><span class="flex gap-3"><a class="text-cyan-300" href="{{ route('admin.users.edit',$user) }}">Ред.</a><form method="POST" action="{{ route('admin.users.destroy',$user) }}">@csrf @method('DELETE')<button class="text-red-300">Удал.</button></form></span></div>@endforeach</div>
<div class="mt-6">{{ $users->links() }}</div>
@endsection

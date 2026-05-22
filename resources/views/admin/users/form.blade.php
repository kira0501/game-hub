@extends('layouts.admin')

@section('content')
<h1 class="text-3xl font-black">{{ $user->exists ? 'Редактировать пользователя' : 'Создать пользователя' }}</h1>
<form method="POST" action="{{ $user->exists ? route('admin.users.update',$user) : route('admin.users.store') }}" class="mt-6 max-w-2xl space-y-4 rounded-lg border border-white/10 bg-white/5 p-5">@csrf @if($user->exists) @method('PUT') @endif
    <input name="name" value="{{ old('name',$user->name) }}" class="hub-input" placeholder="Имя"><input name="email" value="{{ old('email',$user->email) }}" class="hub-input" placeholder="Email"><input name="avatar" value="{{ old('avatar',$user->avatar) }}" class="hub-input" placeholder="Avatar URL">
    <select name="role" class="hub-select"><option value="user" @selected(old('role',$user->role)==='user')>Пользователь</option><option value="admin" @selected(old('role',$user->role)==='admin')>Администратор</option></select>
    <select name="status" class="hub-select"><option value="active" @selected(old('status',$user->status)==='active')>Активен</option><option value="blocked" @selected(old('status',$user->status)==='blocked')>Заблокирован</option></select>
    <input name="password" type="password" class="hub-input" placeholder="Пароль"><input name="password_confirmation" type="password" class="hub-input" placeholder="Повтор пароля">
    @if($errors->any())<div class="text-red-300">{{ $errors->first() }}</div>@endif
    <button class="hub-btn">Сохранить</button>
</form>
@endsection

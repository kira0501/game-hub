@extends('layouts.app')

@section('content')
<section class="hub-container max-w-3xl py-10">
    <h1 class="text-3xl font-black text-white">Профиль</h1>
    <form method="POST" action="{{ route('profile.update') }}" class="hub-panel mt-8 grid gap-4 p-5">@csrf @method('PATCH')
        <input name="name" value="{{ old('name', $user->name) }}" class="hub-input" placeholder="Имя">
        <input name="email" value="{{ old('email', $user->email) }}" class="hub-input" placeholder="Email">
        <input name="avatar" value="{{ old('avatar', $user->avatar) }}" class="hub-input" placeholder="URL аватара">
        <input name="password" type="password" class="hub-input" placeholder="Новый пароль">
        <input name="password_confirmation" type="password" class="hub-input" placeholder="Повторите пароль">
        @if($errors->any())<div class="text-sm text-red-300">{{ $errors->first() }}</div>@endif
        <button class="hub-btn w-fit">Сохранить</button>
    </form>
</section>
@endsection

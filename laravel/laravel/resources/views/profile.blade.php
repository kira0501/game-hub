@extends('layout')

@section('content')
<h1>Личный кабинет</h1>

<div class="profile-grid">
    <div class="card">
        <div class="avatar">{{ mb_substr(auth()->user()->fullname, 0, 1) }}</div>
        <h2>{{ auth()->user()->fullname }}</h2>
        <p>Email: {{ auth()->user()->email }}</p>
        <p>Логин: {{ auth()->user()->login }}</p>
        <p>Роль: {{ auth()->user()->role }}</p>
    </div>

    <div class="card stat-card">
        <span>Набрано XP</span>
        <strong>{{ auth()->user()->xp }}</strong>
        <p>Баллы начисляются после успешного прохождения уроков.</p>
    </div>
</div>

<div class="card">
    <h2>Прогресс обучения</h2>
    @php($completedLessons = auth()->user()->completedLessons()->count())
    @if($completedLessons > 0)
        <p>Пройдено уроков: {{ $completedLessons }}</p>
    @else
        <p>Уроки пока не пройдены. Перейдите в каталог курсов и начните обучение.</p>
    @endif
</div>
@endsection

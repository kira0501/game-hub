@extends('layout')

@section('content')
<h1>{{ $course->title }}</h1>
<p class="lead">{{ $course->description }}</p>

<div class="card">
    <h2>Уроки курса</h2>
    @forelse($course->lessons as $lesson)
        <div class="lesson-row">
            <div>
                <span class="badge">+{{ $lesson->xp_reward }} XP</span>
                <h3>{{ $lesson->title }}</h3>
            </div>
            <a class="button" href="{{ route('lessons.show', $lesson) }}">Начать</a>
        </div>
    @empty
        <p>В этом курсе пока нет уроков.</p>
    @endforelse
</div>
@endsection

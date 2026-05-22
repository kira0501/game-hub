@extends('layout')

@section('content')
<h1>Каталог курсов</h1>

<div class="courses">
    @forelse($courses as $course)
        <article class="course">
            <span class="badge">{{ $course->level }}</span>
            <h2>{{ $course->title }}</h2>
            <p>{{ $course->description }}</p>
            <p>Уроков: {{ $course->lessons_count }}</p>
            <a class="button" href="{{ route('courses.show', $course) }}">Открыть курс</a>
        </article>
    @empty
        <div class="card">
            <p>Курсы пока не добавлены. Администратор сможет добавить их после подключения базы данных.</p>
        </div>
    @endforelse
</div>
@endsection

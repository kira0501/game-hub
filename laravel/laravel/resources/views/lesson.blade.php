@extends('layout')

@section('content')
<a href="{{ route('courses.show', $lesson->course) }}">Назад к курсу</a>

<article class="card lesson-card">
    <span class="badge">{{ $lesson->course->level }}</span>
    <h1>{{ $lesson->title }}</h1>
    <div class="lesson-content">
        {!! nl2br(e($lesson->content)) !!}
    </div>

    <form method="POST" action="{{ route('lessons.complete', $lesson) }}">
        @csrf

        @if($lesson->task_question)
            <label for="answer">{{ $lesson->task_question }}</label>
            <input id="answer" name="answer" value="{{ old('answer') }}" placeholder="Введите ответ">
            @error('answer')
                <div class="error">{{ $message }}</div>
            @enderror
        @endif

        <button type="submit">Завершить урок (+{{ $lesson->xp_reward }} XP)</button>
    </form>
</article>
@endsection

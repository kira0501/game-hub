@extends('layout')

@section('content')
<h1>Административная панель</h1>

<div class="admin-grid">
    <section class="card">
        <h2>Создать курс</h2>
        <form method="POST" action="{{ route('courses.store') }}">
            @csrf
            <label>Название курса</label>
            <input name="title" value="{{ old('title') }}" required>

            <label>Уровень</label>
            <select name="level" required>
                <option value="A1">A1</option>
                <option value="A2">A2</option>
                <option value="B1">B1</option>
                <option value="B2">B2</option>
            </select>

            <label>Описание</label>
            <textarea name="description">{{ old('description') }}</textarea>

            <label class="checkbox">
                <input type="checkbox" name="is_published" value="1" checked>
                Опубликовать
            </label>

            <button type="submit">Добавить курс</button>
        </form>
    </section>

    <section class="card">
        <h2>Создать урок</h2>
        <form method="POST" action="{{ $courses->first() ? route('lessons.store', $courses->first()) : '#' }}">
            @csrf

            <label>Курс</label>
            <select name="course_selector" onchange="this.form.action=this.value" required>
                @foreach($courses as $course)
                    <option value="{{ route('lessons.store', $course) }}">{{ $course->title }}</option>
                @endforeach
            </select>

            <label>Название урока</label>
            <input name="title" required>

            <label>Материал урока</label>
            <textarea name="content" required></textarea>

            <label>Вопрос для проверки</label>
            <input name="task_question" placeholder="Например: Как переводится слово hello?">

            <label>Правильный ответ</label>
            <input name="correct_answer">

            <label>XP за урок</label>
            <input name="xp_reward" type="number" value="10" min="0">

            <label>Порядок</label>
            <input name="sort_order" type="number" value="1" min="1">

            <label class="checkbox">
                <input type="checkbox" name="is_published" value="1" checked>
                Опубликовать
            </label>

            <button type="submit" @if($courses->isEmpty()) disabled @endif>Добавить урок</button>
        </form>
    </section>
</div>

<section class="card">
    <h2>Курсы</h2>
    @forelse($courses as $course)
        <div class="lesson-row">
            <div>
                <h3>{{ $course->title }} ({{ $course->level }})</h3>
                <p>Уроков: {{ $course->lessons_count }}</p>
            </div>
            <span class="badge">{{ $course->is_published ? 'Опубликован' : 'Черновик' }}</span>
        </div>
    @empty
        <p>Курсов пока нет.</p>
    @endforelse
</section>

<section class="card">
    <h2>Пользователи</h2>
    @forelse($users as $user)
        <div class="lesson-row">
            <div>
                <h3>{{ $user->fullname }}</h3>
                <p>{{ $user->email }} | {{ $user->role }}</p>
            </div>
            <span class="badge">{{ $user->xp }} XP</span>
        </div>
    @empty
        <p>Пользователей пока нет.</p>
    @endforelse
</section>
@endsection

@extends('layout')

@section('content')
<section class="hero">
    <div>
        <p class="eyebrow">Русский язык как иностранный</p>
        <h1>Изучайте русский язык с понятными курсами, уроками и прогрессом</h1>
        <p>
            RuLang помогает иностранным студентам изучать алфавит, грамматику,
            лексику и практические задания. После подключения базы данных сайт
            будет сохранять XP, уроки и результаты пользователей.
        </p>

        <div class="actions">
            @auth
                <a class="button" href="{{ route('courses') }}">Перейти к курсам</a>
            @else
                <a class="button" href="{{ route('register') }}">Начать обучение</a>
                <a class="button secondary" href="{{ route('login') }}">Войти</a>
            @endauth
        </div>
    </div>
</section>

<section>
    <h2>Разделы платформы</h2>
    <div class="courses">
        <div class="course">
            <span class="badge">A1</span>
            <h3>Алфавит</h3>
            <p>Буквы, звуки, простые слова и первые упражнения.</p>
        </div>
        <div class="course">
            <span class="badge">A2</span>
            <h3>Грамматика</h3>
            <p>Падежи, род, число и базовые правила построения фраз.</p>
        </div>
        <div class="course">
            <span class="badge">B1</span>
            <h3>Практика</h3>
            <p>Тесты, ответы, начисление XP и отслеживание прогресса.</p>
        </div>
    </div>
</section>
@endsection

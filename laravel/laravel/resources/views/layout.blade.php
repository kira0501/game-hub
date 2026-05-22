<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>RuLang</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<header>
    <div class="brand">
        <a href="{{ route('index') }}">RuLang</a>
    </div>

    <nav>
        <a href="{{ route('index') }}">Главная</a>

        @auth
            <a href="{{ route('courses') }}">Курсы</a>
            <a href="{{ route('profile') }}">Личный кабинет</a>

            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin') }}">Админ</a>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="link-button">Выход</button>
            </form>
        @else
            <a href="{{ route('login') }}">Вход</a>
            <a href="{{ route('register') }}">Регистрация</a>
        @endauth
    </nav>
</header>

<main class="container">
    @if(session('status'))
        <div class="alert">{{ session('status') }}</div>
    @endif

    @yield('content')
</main>
</body>
</html>

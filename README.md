# Game Hub

Laravel 11 дипломный веб-проект: библиотека видеоигр с каталогом, рекомендациями, отзывами, избранным, проверкой совместимости ПК, сравнением цен Steam/Epic Games и административной панелью.

## Стек

- PHP 8.3
- Laravel 11
- MySQL
- Blade
- Tailwind CSS
- Alpine.js
- Laravel Breeze-compatible auth views/controllers

## Установка

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Создайте базу MySQL `game_hub` и проверьте настройки в `.env`.

```bash
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

## Доступы

- Администратор: `admin@gamehub.test` / `password`
- Пользователь: `user@gamehub.test` / `password`

## Основные маршруты

- `/` главная
- `/games` каталог
- `/games/{slug}` страница игры
- `/genres/{slug}` жанр
- `/compare-prices` сравнение цен
- `/recommendations` рекомендации
- `/pc-check` проверка ПК
- `/favorites` избранное
- `/admin` админ-панель

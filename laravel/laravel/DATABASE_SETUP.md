# Подключение MySQL на другом компьютере

Проект подготовлен к работе с базой данных MySQL. На текущем компьютере база не требуется.

## 1. Настроить `.env`

```env
APP_NAME=RuLang
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rulang
DB_USERNAME=root
DB_PASSWORD=
```

Если пароль у MySQL есть, укажите его в `DB_PASSWORD`.

## 2. Создать базу данных

В phpMyAdmin или MySQL Console создайте базу:

```sql
CREATE DATABASE rulang CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 3. Выполнить миграции

```bash
php artisan migrate
```

Для заполнения тестовыми курсами и администратором:

```bash
php artisan migrate --seed
```

Данные администратора после `--seed`:

```text
Email: admin@rulang.test
Password: password
```

## 4. Что уже подготовлено

- Laravel Auth через `Auth::routes()`.
- Таблица `users` с полями `fullname`, `login`, `phone`, `email`, `password`, `xp`, `role`.
- Таблица `courses` для курсов.
- Таблица `lessons` для уроков.
- Таблица `lesson_progress` для прогресса пользователя.
- Роли `student` и `admin`.
- Админ-панель защищена middleware `admin`.
- Прогресс и XP не хранятся в session, а рассчитаны на работу через базу.

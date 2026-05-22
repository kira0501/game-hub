# Game Hub на Vercel

Проект подготовлен для Vercel через PHP community runtime `vercel-php@0.7.4`, который соответствует PHP 8.3.

## 1. База данных

Vercel не использует локальную MySQL из Laragon. Создай внешнюю MySQL базу, например Railway, Aiven, Clever Cloud, PlanetScale или другой MySQL-хостинг.

После создания базы добавь в Vercel переменные:

```env
APP_NAME="Game Hub"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://your-project.vercel.app
LOG_CHANNEL=stderr
DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
VIEW_COMPILED_PATH=/tmp/game-hub/storage/framework/views
```

`APP_KEY` можно взять из локального `.env` или создать командой:

```bash
php artisan key:generate --show
```

## 2. Миграции и сиды

Перед деплоем или после настройки env выполни миграции против внешней базы:

```bash
php artisan migrate:fresh --seed
```

Для этого локальный `.env` временно можно переключить на внешнюю MySQL, выполнить команду, а потом вернуть локальные настройки.

## 3. Настройки проекта в Vercel

В настройках импорта проекта:

- Application Preset: `Other`
- Root Directory: `./`
- Build Command: оставить пустым
- Output Directory: оставить пустым
- Install Command: оставить пустым

`composer install` вручную в Build Command писать не нужно. Runtime `vercel-php` сам устанавливает Composer-зависимости, а Vite-сборка запускается через composer script `vercel`.

## 4. Деплой

Установи и запусти Vercel CLI:

```bash
npm i -g vercel
vercel login
vercel
```

Для production-деплоя:

```bash
vercel --prod
```

## 5. Проверка

После деплоя открой:

- `/`
- `/games`
- `/recommendations`
- любую страницу игры
- `/login`

Если страница открылась без стилей, проверь, что `npm run build` проходит без ошибок и что папка `public/build` попала в деплой.

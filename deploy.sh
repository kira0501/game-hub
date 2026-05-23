#!/usr/bin/env bash
set -euo pipefail

cd /home/o/olivera9/gamehub67

PHP_BIN="/usr/local/bin/php8.3"
COMPOSER_PHAR="/usr/local/bin/composer-phar"

if [ ! -x "$PHP_BIN" ]; then
    PHP_BIN="php"
fi

echo "1/8 Получаю свежие файлы с GitHub..."
git fetch origin main
git reset --hard origin/main

echo "2/8 Готовлю Laravel storage/cache..."
mkdir -p bootstrap/cache \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    public_html
chmod -R u+rwX bootstrap/cache storage

echo "3/8 Ставлю PHP-зависимости..."
if [ -f "$COMPOSER_PHAR" ]; then
    "$PHP_BIN" "$COMPOSER_PHAR" install --no-dev --optimize-autoloader
else
    composer install --no-dev --optimize-autoloader
fi

echo "4/8 Обновляю публичную папку сайта..."
rm -f public/hot public_html/hot
rm -rf public_html/build
cp -R public/build public_html/build
cp public/index.php public_html/index.php

cat > public_html/.htaccess <<'HTACCESS'
AddHandler application/x-httpd-php83 .php

<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f

    RewriteRule ^ index.php [L]
</IfModule>
HTACCESS

echo "5/8 Обновляю базу без удаления данных..."
"$PHP_BIN" artisan migrate --force

echo "6/8 Настраиваю storage-link..."
"$PHP_BIN" artisan storage:link || true

echo "7/8 Чищу и собираю Laravel cache..."
"$PHP_BIN" artisan optimize:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

echo "8/8 Проверяю ассеты..."
test -f public_html/build/manifest.json
find public_html/build/assets -name '*.css' -o -name '*.js' | head -n 5

echo "Готово. Сайт обновлён."

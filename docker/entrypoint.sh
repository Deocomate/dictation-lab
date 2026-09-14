#!/bin/sh
set -e

cd /var/www/html

# Named volumes can mount empty on first boot; recreate the writable tree.
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache
touch storage/logs/laravel.log

php artisan storage:link --force

if [ -z "$APP_KEY" ]; then
    echo "[WARNING] APP_KEY is empty or not set. Web requests may fail with HTTP 500."
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

# Ensure www-data owns all generated cache, compiled views, and logs.
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

exec "$@"

#!/bin/sh
set -e

cd /var/www/html

# Named volumes can mount empty on first boot; recreate the writable tree.
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public
chown -R www-data:www-data storage bootstrap/cache

php artisan storage:link --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"

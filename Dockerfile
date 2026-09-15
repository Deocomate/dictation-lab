# Laravel 12 app (no Vite/npm — Tailwind via CDN, vanilla JS per docs/rules.md).
# Single container: Apache + mod_php serving public/. Built for Coolify:
# the app never publishes a host port (see docker-compose.yml `expose:`),
# Coolify's Traefik proxy reaches it over the internal network instead.
FROM php:8.2-apache-bookworm

# Fix IPv6 stall & network timeout on Debian mirrors during build
RUN echo 'Acquire::ForceIPv4 "true";' > /etc/apt/apt.conf.d/99network \
    && echo 'Acquire::http::Timeout "30";' >> /etc/apt/apt.conf.d/99network \
    && echo 'Acquire::Retries "3";' >> /etc/apt/apt.conf.d/99network

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN apt-get update && apt-get install -y --no-install-recommends \
        unzip git \
    && install-php-extensions pdo_mysql mbstring exif pcntl bcmath gd zip intl \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php-production.ini /usr/local/etc/php/conf.d/zz-laravel.ini

# Install dependencies first so this layer caches across code-only changes.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist

COPY . .

RUN composer dump-autoload --optimize \
    && php artisan package:discover --ansi \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs storage/app/public bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]

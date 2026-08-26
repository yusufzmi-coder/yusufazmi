# syntax=docker/dockerfile:1
# Production image for Easypanel: nginx + php-fpm + queue worker + scheduler
# in one container, supervised.

# ---------- Stage 1: frontend ----------
FROM php:8.4-cli-alpine AS assets

# composer install resolves platform requirements, so the build stage needs the same
# extensions the app declares -- openspout requires ext-zip, and without it the whole
# build fails before a single asset is compiled.
RUN apk add --no-cache nodejs npm git unzip libzip-dev icu-dev $PHPIZE_DEPS \
    && docker-php-ext-install -j"$(nproc)" zip intl \
    && apk del $PHPIZE_DEPS

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /build

# Wayfinder's Vite plugin shells out to `php artisan`, so the PHP side must exist
# before the frontend can be built.
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .
RUN composer dump-autoload --optimize --no-dev \
    && npm run build \
    && rm -rf node_modules

# ---------- Stage 2: runtime ----------
FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
        nginx supervisor postgresql-client \
        icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev \
        postgresql-dev oniguruma-dev linux-headers $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql pgsql intl zip gd bcmath opcache pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS \
    && rm -rf /tmp/pear /var/cache/apk/*

COPY docker/production/php.ini /usr/local/etc/php/conf.d/99-production.ini
COPY docker/production/nginx.conf /etc/nginx/nginx.conf
COPY docker/production/supervisord.conf /etc/supervisord.conf
COPY docker/production/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html
COPY --from=assets /build .

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rw storage bootstrap/cache

EXPOSE 80

ENTRYPOINT ["entrypoint"]
CMD ["supervisord", "-c", "/etc/supervisord.conf"]

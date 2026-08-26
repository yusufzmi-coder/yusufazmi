# syntax=docker/dockerfile:1
# Local development image: PHP 8.4 + FPM.
FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
        git curl unzip icu-dev libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev \
        postgresql-dev oniguruma-dev linux-headers nodejs npm $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_pgsql pgsql intl zip gd bcmath opcache pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS \
    && rm -rf /tmp/pear

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Match the host user so files written from the container stay editable.
ARG UID=501
ARG GID=20
RUN adduser -u ${UID} -G dialout -s /bin/sh -D app 2>/dev/null || true

COPY docker/app/php.ini /usr/local/etc/php/conf.d/99-app.ini

WORKDIR /var/www/html

CMD ["php-fpm"]

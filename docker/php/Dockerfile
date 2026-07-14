FROM composer:2 AS vendor
WORKDIR /app
COPY src/composer.json src/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

FROM php:8.3-fpm
RUN apt-get update && apt-get install -y libicu-dev libzip-dev zip \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip \
    && apt-get clean

WORKDIR /var/www/html
COPY src/ .
COPY --from=vendor /app/vendor ./vendor
RUN composer dump-autoload --optimize --no-dev \
    && php bin/console cache:warmup --env=prod

COPY conf.d/session.ini /usr/local/etc/php/conf.d/session.ini

CMD ["php-fpm"]
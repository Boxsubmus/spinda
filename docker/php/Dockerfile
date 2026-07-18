# --- Stage 1: PHP dependencies ---
FROM composer:2 AS vendor
WORKDIR /app
COPY src/composer.json src/composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# --- Stage 2: Frontend build (Vite + Vue + Inertia) ---
FROM node:22-slim AS frontend
WORKDIR /app
COPY src/package.json src/package-lock.json ./
RUN npm ci
COPY src/ .
RUN npm run build

# --- Stage 3: Final runtime image ---
FROM php:8.3-fpm
RUN apt-get update && apt-get install -y libicu-dev libzip-dev zip \
    && docker-php-ext-install intl pdo pdo_mysql opcache zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY src/ .
COPY --from=vendor /app/vendor ./vendor
COPY --from=frontend /app/public/build ./public/build

RUN composer dump-autoload --optimize --no-dev

COPY docker/php/conf.d/session.ini /usr/local/etc/php/conf.d/session.ini
COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
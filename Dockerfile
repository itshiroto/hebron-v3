# 1. PHP dependencies (the frontend build imports Ziggy from vendor/)
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# 2. Frontend assets
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# 3. Runtime: nginx + php-fpm, runs migrations/storage:link/optimize on boot (AUTORUN_ENABLED)
FROM serversideup/php:8.4-fpm-nginx
USER root
RUN install-php-extensions intl
USER www-data
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=vendor /app/vendor ./vendor
COPY --chown=www-data:www-data --from=assets /app/public/build ./public/build
# ponytail: SSR bundle not shipped, pages render client-side (same as before); add an SSR service if SEO needs it
RUN composer dump-autoload --optimize --no-dev \
    && mkdir -p database/data

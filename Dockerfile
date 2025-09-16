# Stage 1 - install vendor with PHP 8.3 (tanpa jalankan artisan)
FROM php:8.3-cli-alpine AS vendor
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN apk add --no-cache git unzip
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
WORKDIR /app
COPY ENOTARIS-BACKEND/composer.json ENOTARIS-BACKEND/composer.lock ./
# <-- tambahkan --no-scripts agar artisan tidak dipanggil di stage ini
RUN composer install --no-dev --no-interaction --prefer-dist --no-progress --no-scripts

# Stage 2 - PHP-FPM runtime
FROM php:8.3-fpm-alpine
WORKDIR /var/www/html
RUN docker-php-ext-install pdo pdo_mysql opcache
COPY ENOTARIS-BACKEND /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache
EXPOSE 9000
CMD ["php-fpm"]

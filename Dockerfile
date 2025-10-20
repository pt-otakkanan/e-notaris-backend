# ============================
# Stage 1: Composer (install vendor tanpa artisan)
# ============================
FROM php:8.3-cli-alpine AS vendor

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_HOME=/tmp

# alat bantu buat composer (ambil dist zip atau fallback git)
RUN apk add --no-cache git unzip

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

# copy file dependensi dulu agar cache optimal
COPY composer.json composer.lock ./

# install vendor TANPA menjalankan script/artisan,
# dan abaikan cek ekstensi yang belum ada di stage ini (gd, zip)
RUN composer install \
    --no-dev --no-interaction --prefer-dist --no-progress --no-scripts \
    --ignore-platform-req=ext-gd --ignore-platform-req=ext-zip

# ============================
# Stage 2: Runtime PHP-FPM
# ============================
FROM php:8.3-fpm-alpine

# deps & ekstensi Laravel + PHPWord/FPDF (gd, zip, intl)
RUN apk add --no-cache \
      icu-dev \
      libpng-dev libjpeg-turbo-dev freetype-dev \
      libzip-dev \
      oniguruma-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo pdo_mysql intl opcache gd zip

# (opsional) timezone agar log cocok
ENV TZ=Asia/Jakarta
RUN apk add --no-cache tzdata && \
    cp /usr/share/zoneinfo/Asia/Jakarta /etc/localtime && echo "Asia/Jakarta" > /etc/timezone

WORKDIR /var/www/html

# salin source code backend (karena context = ./ENOTARIS-BACKEND, cukup titik)
COPY . /var/www/html

# bawa vendor dari stage 1
COPY --from=vendor /app/vendor /var/www/html/vendor

# permission Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]

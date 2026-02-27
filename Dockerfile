FROM php:8.2-apache

# System packages and PHP extensions required by Laravel + MySQL.
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
    pdo_mysql \
    mbstring \
    bcmath \
    gd \
    zip \
    exif

# Apache settings for Laravel public/ directory.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN a2enmod rewrite headers \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
      /etc/apache2/sites-available/*.conf \
      /etc/apache2/apache2.conf \
      /etc/apache2/conf-available/*.conf

# Composer binary.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first for better Docker layer caching.
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction --no-scripts

# Copy application source.
COPY . .

# Run Laravel/Composer scripts only after full source exists.
RUN composer dump-autoload --optimize --no-interaction \
    && php artisan package:discover --ansi

# Runtime write permissions.
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

# Laravel app should receive APP_KEY, DB_* and other vars from Render env.
EXPOSE 80

CMD ["apache2-foreground"]

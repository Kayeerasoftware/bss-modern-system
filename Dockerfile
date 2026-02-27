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

# Production PHP tuning.
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.memory_consumption=192"; \
    echo "opcache.interned_strings_buffer=16"; \
    echo "opcache.max_accelerated_files=20000"; \
    echo "opcache.revalidate_freq=0"; \
    echo "opcache.validate_timestamps=0"; \
    echo "realpath_cache_size=4096K"; \
    echo "realpath_cache_ttl=600"; \
  } > /usr/local/etc/php/conf.d/99-performance.ini

# Apache settings for Laravel public/ directory.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN a2enmod rewrite headers deflate expires \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
      /etc/apache2/sites-available/*.conf \
      /etc/apache2/apache2.conf \
      /etc/apache2/conf-available/*.conf

# Compression + static asset caching.
RUN printf '%s\n' \
    '<IfModule mod_deflate.c>' \
    '  AddOutputFilterByType DEFLATE text/plain text/html text/xml text/css application/javascript application/json image/svg+xml' \
    '</IfModule>' \
    '<IfModule mod_expires.c>' \
    '  ExpiresActive On' \
    '  ExpiresByType text/css "access plus 7 days"' \
    '  ExpiresByType application/javascript "access plus 7 days"' \
    '  ExpiresByType image/png "access plus 30 days"' \
    '  ExpiresByType image/jpeg "access plus 30 days"' \
    '  ExpiresByType image/svg+xml "access plus 30 days"' \
    '  ExpiresByType font/woff2 "access plus 30 days"' \
    '</IfModule>' \
    > /etc/apache2/conf-available/performance.conf \
    && a2enconf performance

# Composer binary.
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first for better Docker layer caching.
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --classmap-authoritative --no-interaction --no-scripts

# Copy application source.
COPY . .

# Run Laravel/Composer scripts only after full source exists.
RUN composer dump-autoload --optimize --classmap-authoritative --no-interaction \
    && php artisan package:discover --ansi

# Runtime write permissions.
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache \
    && mkdir -p public/uploads \
    && chown -R www-data:www-data public/uploads \
    && chmod -R ug+rwx public/uploads \
    && ln -sfn /var/www/html/storage/app/public /var/www/html/public/storage

RUN chmod +x scripts/docker-start.sh

# Laravel app should receive APP_KEY, DB_* and other vars from Render env.
EXPOSE 80

CMD ["bash", "scripts/docker-start.sh"]

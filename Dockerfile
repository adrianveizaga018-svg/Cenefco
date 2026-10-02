FROM php:8.2-fpm-alpine AS base

# Dependencias del sistema
RUN apk add --no-cache \
    bash \
    curl \
    freetype-dev \
    git \
    icu-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    mysql-client \
    nodejs \
    npm \
    oniguruma-dev \
    supervisor \
    unzip \
    zip

# Extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        bcmath \
        ctype \
        exif \
        fileinfo \
        gd \
        intl \
        mbstring \
        opcache \
        pdo_mysql \
        pcntl \
        zip

# Redis
RUN pecl install redis && docker-php-ext-enable redis

# Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Dependencias PHP (cache layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Dependencias Node (para Vite)
COPY package.json package-lock.json ./
RUN npm ci

# Código de la aplicación
COPY . .

# Compilar assets con Vite
RUN npm run build

# php.ini personalizado
COPY docker/php.ini /usr/local/etc/php/conf.d/custom.ini

# Permisos
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8000

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]

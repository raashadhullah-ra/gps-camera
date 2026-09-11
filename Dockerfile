# ----------------------------------------------------
# Multi-Stage Dockerfile for Laravel (PHP 8.2 + Apache) on Render
# ----------------------------------------------------

# Stage 1: Build Frontend Assets (Vite)
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Composer Dependencies
FROM composer:2 AS composer-builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts
COPY . .
RUN composer dump-autoload --optimize --no-dev

# Stage 3: Production Runtime (PHP 8.2 Apache)
FROM php:8.2-apache

# Install system dependencies & PHP extension development libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libsqlite3-dev \
    libpq-dev \
    curl \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Configure & Install PHP extensions required by Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        mbstring \
        zip \
        xml \
        bcmath \
        gd \
        intl \
        opcache \
        exif

# Enable Apache mod_rewrite for Laravel routing
RUN a2enmod rewrite

# Setup working directory
WORKDIR /var/www/html

# Copy application source code
COPY . /var/www/html

# Copy built vendor directory from Composer stage
COPY --from=composer-builder /app/vendor /var/www/html/vendor

# Copy compiled frontend assets from Node stage
COPY --from=node-builder /app/public/build /var/www/html/public/build

# Copy Apache virtual host config and entrypoint script
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Setup proper directory permissions
RUN chown -R www-data:www-data /var/www/html /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose default port (Render overrides dynamically via $PORT)
EXPOSE 80

# Run entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

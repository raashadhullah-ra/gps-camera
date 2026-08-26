# ==========================================
# Stage 1: Build Vite Frontend Assets
# ==========================================
FROM node:20-alpine AS frontend
WORKDIR /app

# Install Node dependencies
COPY package*.json ./
RUN npm ci || npm install

# Copy application assets and compile
COPY . .
RUN npm run build

# ==========================================
# Stage 2: PHP 8.2 Production Web Server
# ==========================================
FROM php:8.2-apache

# Install Linux system dependencies and libraries
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libsqlite3-dev \
    libpq-dev \
    libicu-dev \
    libonig-dev \
    zip \
    unzip \
    ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        pdo_pgsql \
        pdo_sqlite \
        gd \
        zip \
        bcmath \
        intl \
        opcache \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache URL rewrite module
RUN a2enmod rewrite

# Copy Composer executable
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy compiled assets from Stage 1
COPY --from=frontend /app/public/build ./public/build

# Install PHP dependencies without development packages
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Setup Apache site configuration and Entrypoint
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Fix Windows CRLF line endings if present and grant execution rights
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

# Configure file ownership and permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose default HTTP port
EXPOSE 80

# Execute entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# ===============================
# Stage 1 - PHP dependencies
# ===============================
FROM composer:2 AS vendor

WORKDIR /var/www/html

# Copy composer files first for caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --ignore-platform-reqs

# ===============================
# Stage 2 - Frontend build
# ===============================
FROM node:20 AS frontend

WORKDIR /var/www/html

# Copy package files first for caching
COPY package.json package-lock.json ./

# Install npm dependencies
RUN npm install

# Copy the rest of the frontend files
COPY . .

# Build the frontend
RUN npm run build

# ===============================
# Stage 3 - Final Laravel App
# ===============================
FROM php:8.2-fpm

# Install needed PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    git \
    curl \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

WORKDIR /var/www/html

# Copy vendor from stage 1
COPY --from=vendor /var/www/html/vendor ./vendor

# Copy built frontend from stage 2
COPY --from=frontend /var/www/html/public ./public

# Copy the rest of the Laravel app
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
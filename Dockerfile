# Base image with PHP, Composer, and Nginx
FROM webdevops/php-nginx:8.2

# Set working directory
WORKDIR /app

# Set document root to Laravel public directory
ENV WEB_DOCUMENT_ROOT=/app/public

# Copy composer and install dependencies first for caching
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# Copy the rest of the app files
COPY . .

# Install Node dependencies and build assets
RUN apt-get update && apt-get install -y npm \
    && npm install \
    && npm run build

# Laravel cache optimizations
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Set permissions
RUN chown -R application:application /app \
    && chmod -R 755 /app/storage /app/bootstrap/cache

EXPOSE 80
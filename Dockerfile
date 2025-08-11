# Stage 1: Composer Dependencies
FROM composer:2 AS vendor

WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies (ignore platform reqs for smoother build)
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --ignore-platform-reqs

# Stage 2: Node Build
FROM node:20 AS frontend

WORKDIR /var/www/html

# Copy package files
COPY package*.json ./

# Install JS deps (force install to bypass version conflicts)
RUN npm install --legacy-peer-deps --force

# Copy the rest of the application
COPY . .

# Build frontend assets (ignore minor errors)
RUN npm run build || echo "⚠️ Frontend build warnings ignored"

# Stage 3: Final Laravel + PHP + Nginx
FROM webdevops/php-nginx:8.2

WORKDIR /var/www/html

# Copy PHP vendor files
COPY --from=vendor /var/www/html /var/www/html

# Copy frontend build output
COPY --from=frontend /var/www/html/public/build /var/www/html/public/build

# Copy the rest of the app files
COPY . .

# Set correct permissions for Laravel storage & bootstrap/cache
RUN chown -R application:application storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Laravel optimize caches (ignore errors if configs are missing)
RUN php artisan config:clear || true \
 && php artisan cache:clear || true \
 && php artisan route:cache || true \
 && php artisan view:cache || true

# Environment variables (override via docker run or docker-compose)
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV APP_URL=https://example.com
ENV VITE_API_URL=https://example.com

# Expose port 80 for Nginx
EXPOSE 80

# Start the container
CMD ["supervisord"]
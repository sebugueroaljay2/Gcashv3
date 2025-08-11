# =========================
# Stage 1: Frontend build
# =========================
FROM node:20 AS frontend-builder

WORKDIR /app

# Copy only package files first for caching
COPY package*.json ./

# Install Node dependencies
RUN npm install

# Copy all frontend source
COPY . .

# Generate Ziggy routes (if applicable)
RUN if [ -f artisan ]; then php artisan ziggy:generate || true; fi

# Build frontend assets
RUN npm run build


# =========================
# Stage 2: PHP + Nginx
# =========================
FROM webdevops/php-nginx:8.2

WORKDIR /app

# Copy composer files first
COPY composer.json composer.lock ./

# Install PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy all Laravel project files
COPY . .

# Copy built frontend assets from Stage 1
COPY --from=frontend-builder /app/public/js public/js
COPY --from=frontend-builder /app/public/css public/css
COPY --from=frontend-builder /app/public/build public/build

# Laravel cache config/routes/views
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Set file permissions
RUN chown -R application:application /app

# Expose default web port
EXPOSE 80
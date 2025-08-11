# Use PHP + Nginx image
FROM webdevops/php-nginx:8.2

# Set working directory
WORKDIR /app

# Copy project files
COPY . .

# Update & install Node.js + npm + git (for ziggy)
RUN apt-get update && apt-get install -y npm git

# Install PHP dependencies (no-dev for production)
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies
RUN npm install

# Generate Ziggy routes (only if you're using ziggy in frontend)
RUN php artisan ziggy:generate

# Build frontend
RUN npm run build

# Laravel cache config/routes/views
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Set file permissions
RUN chown -R application:application /app

# Expose default web port
EXPOSE 80
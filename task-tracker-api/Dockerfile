FROM php:8.2-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    bash \
    nginx \
    postgresql-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files and install dependencies first (layer cache)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Copy the rest of the application
COPY . .

# Finish composer setup
RUN composer dump-autoload --optimize

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf

EXPOSE 8000

# Entrypoint: run migrations then start PHP-FPM + Nginx
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

CMD ["/entrypoint.sh"]

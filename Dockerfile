# --- Stage 1: Build frontend assets ---
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# --- Stage 2: Install PHP dependencies ---
FROM composer:2.7 AS composer-builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --no-dev --optimize

# --- Stage 3: Production Image ---
FROM php:8.3-fpm-alpine

# Install mlocati PHP extensions installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install base packages & PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    bash \
    && install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    xml \
    zip \
    gd \
    opcache \
    bcmath \
    intl

# Configure Nginx and Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh

# Set working directory
WORKDIR /var/www/html

# Copy application files from builders
COPY --from=composer-builder --chown=www-data:www-data /app /var/www/html
COPY --from=node-builder --chown=www-data:www-data /app/public /var/www/html/public

# Ensure write permissions for storage & cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose default port (Render overrides this with $PORT)
EXPOSE 8080

# Environment variables
ENV APP_ENV=production
ENV APP_DEBUG=false

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

#!/bin/sh
set -e

# Set port dynamically from $PORT, default to 8080
PORT=${PORT:-8080}
echo "Configuring Nginx port to $PORT..."
sed -i "s/PORT_PLACEHOLDER/${PORT}/g" /etc/nginx/nginx.conf

# Run configuration caching for production
if [ "$APP_ENV" = "production" ]; then
    echo "Running production optimizations..."
    php artisan config:cache || echo "Failed to cache config"
    php artisan route:cache || echo "Failed to cache routes"
    php artisan view:cache || echo "Failed to cache views"
fi

# Execute CMD (which will be supervisor)
exec "$@"

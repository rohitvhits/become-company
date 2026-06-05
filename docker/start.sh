#!/bin/sh
set -e

echo "📥 Fetching .env from S3..."

aws s3 cp \
  s3://nybest-env-config/patient-env/.env \
  /var/www/html/.env

echo "✅ .env loaded"

# Clear Laravel caches safely
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "🚀 Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf


#!/bin/sh

echo "=== Container starting ==="
echo "PORT=${PORT}"
echo "PWD=$(pwd)"

echo "=== Generating .env file ==="

RESOLVED_DB_HOST="${DB_HOST:-${MYSQLHOST:-127.0.0.1}}"
RESOLVED_DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
RESOLVED_DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-db_webgis}}"
RESOLVED_DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-root}}"
RESOLVED_DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"
RESOLVED_APP_URL="${APP_URL:-http://localhost}"
RESOLVED_APP_ENV="${APP_ENV:-production}"
RESOLVED_APP_DEBUG="${APP_DEBUG:-false}"
RESOLVED_APP_NAME="${APP_NAME:-WebGIS}"
RESOLVED_APP_KEY="${APP_KEY:-}"
RESOLVED_PORT="${PORT:-8000}"

cat > /var/www/.env << ENVFILE
APP_NAME=${RESOLVED_APP_NAME}
APP_ENV=${RESOLVED_APP_ENV}
APP_KEY=${RESOLVED_APP_KEY}
APP_DEBUG=${RESOLVED_APP_DEBUG}
APP_URL=${RESOLVED_APP_URL}

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${RESOLVED_DB_HOST}
DB_PORT=${RESOLVED_DB_PORT}
DB_DATABASE=${RESOLVED_DB_DATABASE}
DB_USERNAME=${RESOLVED_DB_USERNAME}
DB_PASSWORD=${RESOLVED_DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
CACHE_STORE=${CACHE_STORE:-database}

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME=${RESOLVED_APP_NAME}
ENVFILE

echo "=== .env generated ==="
echo "DB_HOST=${RESOLVED_DB_HOST}"
echo "DB_PORT=${RESOLVED_DB_PORT}"
echo "DB_DATABASE=${RESOLVED_DB_DATABASE}"
echo "APP_KEY SET: $([ -n "${RESOLVED_APP_KEY}" ] && echo YES || echo NO)"

echo "=== Syncing bundled images to volume ==="
# Copy bundled images from image to volume (only if not already there)
# This preserves existing uploaded files while seeding initial images
if [ -d "/var/www/storage/app/public/wisata" ]; then
    BUNDLED_DIR="/var/www/storage/app/public/wisata"
    for f in "$BUNDLED_DIR"/*; do
        fname=$(basename "$f")
        if [ ! -f "/var/www/storage/app/public/wisata/$fname" ]; then
            cp "$f" "/var/www/storage/app/public/wisata/$fname"
            echo "Copied: $fname"
        fi
    done
    echo "Image sync done."
else
    echo "No bundled images found, skipping sync."
fi

echo "=== Setting storage permissions ==="
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

echo "=== Linking storage ==="
rm -f /var/www/public/storage
php artisan storage:link 2>&1 || true

echo "=== Running migrations ==="
php artisan migrate --force 2>&1 || echo "Migration failed or nothing to migrate"

echo "=== Caching config ==="
php artisan config:cache 2>&1 || true

echo "=== Caching routes ==="
php artisan route:cache 2>&1 || true

echo "=== Starting PHP server on 0.0.0.0:${RESOLVED_PORT} ==="
exec php artisan serve --host=0.0.0.0 --port=${RESOLVED_PORT}

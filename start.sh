#!/bin/sh
set -e

echo "=== Generating .env file ==="

# Resolve DB variables with fallback to MYSQL* vars
RESOLVED_DB_HOST="${DB_HOST:-${MYSQLHOST}}"
RESOLVED_DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
RESOLVED_DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE}}"
RESOLVED_DB_USERNAME="${DB_USERNAME:-${MYSQLUSER}}"
RESOLVED_DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD}}"
RESOLVED_APP_URL="${APP_URL:-http://localhost}"
RESOLVED_APP_ENV="${APP_ENV:-production}"
RESOLVED_APP_DEBUG="${APP_DEBUG:-false}"
RESOLVED_APP_NAME="${APP_NAME:-WebGIS}"

cat > /var/www/.env << ENVFILE
APP_NAME=${RESOLVED_APP_NAME}
APP_ENV=${RESOLVED_APP_ENV}
APP_KEY=${APP_KEY}
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
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${RESOLVED_APP_NAME}"
ENVFILE

echo "=== .env generated ==="
echo "DB_HOST=${RESOLVED_DB_HOST}"
echo "DB_PORT=${RESOLVED_DB_PORT}"
echo "DB_DATABASE=${RESOLVED_DB_DATABASE}"
echo "APP_KEY set: $([ -n "${APP_KEY}" ] && echo YES || echo NO)"

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Linking storage ==="
php artisan storage:link || true

echo "=== Caching config ==="
php artisan config:cache || true

echo "=== Caching routes ==="
php artisan route:cache || true

echo "=== Starting server on port ${PORT:-8000} ==="
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

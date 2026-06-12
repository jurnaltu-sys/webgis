#!/bin/sh
set -e

echo "=== Generating .env file ==="
cat > /var/www/.env << EOF
APP_NAME=${APP_NAME:-WebGIS}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST:-${MYSQLHOST}}
DB_PORT=${DB_PORT:-${MYSQLPORT:-3306}}
DB_DATABASE=${DB_DATABASE:-${MYSQLDATABASE}}
DB_USERNAME=${DB_USERNAME:-${MYSQLUSER}}
DB_PASSWORD=${DB_PASSWORD:-${MYSQLPASSWORD}}

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
MAIL_FROM_NAME="\${APP_NAME}"
EOF

echo "=== .env generated ==="
echo "DB_HOST=${DB_HOST}"
echo "DB_PORT=${DB_PORT}"
echo "DB_DATABASE=${DB_DATABASE}"

echo "=== Running migrations ==="
php artisan migrate --force

echo "=== Linking storage ==="
php artisan storage:link || true

echo "=== Caching config ==="
php artisan config:cache

echo "=== Caching routes ==="
php artisan route:cache

echo "=== Starting server on port ${PORT:-8000} ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}

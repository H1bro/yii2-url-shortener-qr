#!/usr/bin/env bash
set -euo pipefail

APP_PATH="/var/www/html"
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USER:-root}"
DB_PASSWORD="${DB_PASSWORD:-root}"
DB_NAME="${DB_NAME:-yii2_shortener}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-1}"

cd "${APP_PATH}"

echo "Installing composer runtime dependencies..."
composer install --no-interaction --prefer-dist --no-dev

echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."
until mysqladmin ping \
  --protocol=tcp \
  --skip-ssl \
  -h"${DB_HOST}" \
  -P"${DB_PORT}" \
  -u"${DB_USER}" \
  -p"${DB_PASSWORD}" \
  --silent; do
  sleep 2
done
echo "MySQL is available."

mkdir -p runtime web/assets
chmod -R 0777 runtime web/assets

if [ "${RUN_MIGRATIONS}" = "1" ]; then
  echo "Running migrations..."
  php yii migrate --interactive=0
fi

exec "$@"

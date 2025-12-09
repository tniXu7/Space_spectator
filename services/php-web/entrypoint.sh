#!/usr/bin/env bash
set -e

APP_DIR="/var/www/html"
PATCH_DIR="/opt/laravel-patches"

echo "[php] init start"

if [ ! -f "$APP_DIR/artisan" ]; then
  echo "[php] creating laravel skeleton"
  composer create-project --no-interaction --prefer-dist laravel/laravel:^11 "$APP_DIR" || {
    echo "[php] ERROR: Failed to create Laravel project"
    exit 1
  }
  cp "$APP_DIR/.env.example" "$APP_DIR/.env" || true
  sed -i 's|APP_NAME=Laravel|APP_NAME=ISSOSDR|g' "$APP_DIR/.env" || true
  php "$APP_DIR/artisan" key:generate || true
  echo "[php] Laravel skeleton created"
fi

if [ -d "$PATCH_DIR" ]; then
  echo "[php] applying patches"
  rsync -a "$PATCH_DIR/" "$APP_DIR/" || {
    echo "[php] WARNING: Failed to apply some patches, continuing..."
  }
fi

# Убеждаемся, что директории существуют
mkdir -p "$APP_DIR/storage/logs" "$APP_DIR/storage/framework/cache" "$APP_DIR/storage/framework/sessions" "$APP_DIR/storage/framework/views" "$APP_DIR/bootstrap/cache"

chown -R www-data:www-data "$APP_DIR" || true
chmod -R 775 "$APP_DIR/storage" "$APP_DIR/bootstrap/cache" || true

# Настраиваем PHP-FPM для прослушивания на всех интерфейсах
# Ищем конфигурационный файл PHP-FPM
PHP_FPM_CONF="/usr/local/etc/php-fpm.d/www.conf"
if [ -f "$PHP_FPM_CONF" ]; then
  # Заменяем listen на 0.0.0.0:9000 если он указан как 127.0.0.1:9000 или как socket
  sed -i 's/listen = 127.0.0.1:9000/listen = 0.0.0.0:9000/' "$PHP_FPM_CONF" 2>/dev/null || true
  sed -i 's/listen = \/run\/php\/php.*-fpm.sock/listen = 0.0.0.0:9000/' "$PHP_FPM_CONF" 2>/dev/null || true
  # Если listen не найден, добавляем его
  if ! grep -q "^listen = " "$PHP_FPM_CONF"; then
    echo "listen = 0.0.0.0:9000" >> "$PHP_FPM_CONF"
  fi
fi

echo "[php] starting php-fpm"
exec php-fpm -F

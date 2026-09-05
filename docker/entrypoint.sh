#!/bin/bash
set -e

echo "==> Esperando base de datos..."
until php artisan db:show --json > /dev/null 2>&1; do
    echo "    Base de datos no disponible, reintentando en 2s..."
    sleep 2
done

echo "==> Ejecutando migraciones..."
php artisan migrate --force

echo "==> Generando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Creando storage link..."
php artisan storage:link 2>/dev/null || true

echo "==> Listo. Iniciando servicio..."
exec "$@"

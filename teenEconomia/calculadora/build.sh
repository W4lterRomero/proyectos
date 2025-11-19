#!/usr/bin/env bash
# exit on error
set -o errexit

# Instalar dependencias de Composer
composer install --no-dev --optimize-autoloader

# Crear archivo .env si no existe
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generar APP_KEY si no existe
php artisan key:generate --force --no-interaction

# Crear directorio de base de datos si no existe
mkdir -p database

# Crear archivo SQLite si no existe
touch database/database.sqlite

# Ejecutar migraciones
php artisan migrate --force --no-interaction

# Limpiar y cachear configuraciones
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Instalar dependencias de NPM y compilar assets
npm install
npm run build

# Dar permisos a directorios necesarios
chmod -R 775 storage bootstrap/cache

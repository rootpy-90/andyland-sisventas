#!/bin/bash

# Script de entrypoint para producción
# Maneja la primera ejecución y actualizaciones

set -e

echo "=== Iniciando Andyland SisVentas ==="

# Esperar a que MySQL esté listo
echo "Esperando conexión a MySQL..."
until php -r "try { new PDO('mysql:host=db;dbname=dbventaslaravel', 'andyland_user', 'A7SDY371Q'); echo 'OK'; } catch(Exception \$e) { exit(1); }" 2>/dev/null; do
    echo "MySQL no está listo aún, esperando 2 segundos..."
    sleep 2
done

echo "✓ MySQL conectado"

# Verificar si es la primera ejecución
if [ ! -f /var/www/storage/app/.installed ]; then
    echo ""
    echo "=== Primera ejecución detectada ==="
    
    # Generar APP_KEY si no existe
    if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
        echo "Generando APP_KEY..."
        php artisan key:generate --force --show > /tmp/app_key
        export APP_KEY=$(cat /tmp/app_key)
        echo "APP_KEY=$APP_KEY" >> /var/www/.env
        rm /tmp/app_key
    fi
    
    # Ejecutar migraciones
    echo "Ejecutando migraciones..."
    php artisan migrate --force
    
    # Ejecutar seeders (crear admin y datos iniciales)
    echo "Ejecutando seeders (creando admin y datos de prueba)..."
    php artisan db:seed --force
    
    # Crear link de storage
    php artisan storage:link || true
    
    # Marcar como instalado
    touch /var/www/storage/app/.installed
    
    echo ""
    echo "✓ Aplicación configurada correctamente"
    echo "✓ Admin creado: admin@andyland.com / admin123"
    echo ""
else
    echo ""
    echo "=== Aplicación ya instalada ==="
    echo "Ejecutando migraciones pendientes..."
    php artisan migrate --force
fi

# Optimizar para producción (cada vez que inicia)
echo "Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Asegurar permisos correctos
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache 2>/dev/null || true

echo ""
echo "=== Iniciando servidor web ==="
echo "✓ Andyland SisVentas está listo"
echo "✓ URL: https://andyland-sisventas.ndeapp.com"
echo ""

# Iniciar Apache en primer plano
exec apache2-foreground

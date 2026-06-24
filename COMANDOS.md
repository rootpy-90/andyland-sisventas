# 🚀 Comandos Útiles - Andyland SisVentas

## 🐳 DOCKER COMPOSE (Recomendado)

### Levantar todo el stack
```bash
# Primera vez - construye imágenes y levanta
docker-compose up -d --build

# Ver logs en tiempo real
docker-compose logs -f app

# Ver solo logs de errores
docker-compose logs -f --tail=50 app
```

### Acceder a la aplicación
- **App:** http://localhost:8000
- **phpMyAdmin:** http://localhost:8081 (perfil: tools)
  - Usuario: root / Contraseña: rootsecret

### Comandos dentro del contenedor
```bash
# Entrar al contenedor app
docker-compose exec app bash

# Una vez dentro del contenedor:
php artisan migrate              # Ejecutar migraciones
php artisan db:seed              # Ejecutar seeders
php artisan config:cache         # Cachear configuración
php artisan route:list           # Ver todas las rutas
php artisan tinker               # Console interactiva
composer install                 # Instalar dependencias
npm install && npm run dev       # Instalar y compilar assets
```

### Levantar phpMyAdmin (opcional)
```bash
docker-compose --profile tools up -d
```

### Detener servicios
```bash
# Detener sin borrar volúmenes
docker-compose down

# Detener y borrar volúmenes (⚠️ borra la BD)
docker-compose down -v
```

---

## 🔧 SETUP LOCAL (sin Docker)

### Requisitos
- PHP 7.4+ con extensiones: mbstring, pdo_mysql, gd, zip
- Composer 2.x
- Node.js 16+ y npm
- MySQL 8.0 en puerto 3307

### Instalación
```bash
# 1. Instalar dependencias PHP
composer install

# 2. Instalar dependencias Node
npm install

# 3. Copiar .env y configurar
cp .env.example .env
# Editar .env con valores locales (DB_PORT=3307, DB_USERNAME=root, etc.)

# 4. Generar APP_KEY
php artisan key:generate

# 5. Crear BD manualmente (si no usas Docker)
mysql -u root -p -e "CREATE DATABASE dbventaslaravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 6. Ejecutar script de inicialización
mysql -u root -p dbventaslaravel < docker/mysql/init.sql

# 7. Migraciones (si existen)
php artisan migrate

# 8. Compilar assets
npm run dev

# 9. Levantar servidor
php artisan serve --port=8000
```

---

## 🎯 COMANDOS ARTISAN ÚTILES

### Base de datos
```bash
php artisan migrate:status         # Ver estado de migraciones
php artisan migrate:rollback       # Revertir última migración
php artisan migrate:refresh        # Rollback + migrate (⚠️ borra datos)
php artisan db:seed --class=NombreSeeder  # Ejecutar seeder específico
```

### Caché
```bash
php artisan config:clear           # Limpiar caché de configuración
php artisan route:clear            # Limpiar caché de rutas
php artisan view:clear             # Limpiar caché de vistas
php artisan cache:clear            # Limpiar toda la caché
```

### Desarrollo
```bash
php artisan make:model Nombre -mcr # Crear modelo + migración + controller
php artisan make:controller Nombre # Crear controller
php artisan make:migration nombre  # Crear migración
php artisan route:list --json      # Ver rutas en formato JSON
```

---

## 🧪 ACCESOS RÁPIDOS

### Usuarios de prueba (después de ejecutar init.sql)
- **Admin:** admin@andyland.com / admin123
- **Cliente:** Registrar desde /register

### URLs importantes
- Tienda: http://localhost:8000/tienda
- Admin: http://localhost:8000/home (requiere idrol=1)
- Perfil: http://localhost:8000/tienda/perfil
- Mis Compras: http://localhost:8000/tienda/mis-compras
- API Artículos: http://localhost:8000/api/articulos
- API Dashboard: http://localhost:8000/api/dashboard/stats

---

## 🐛 DEBUGGING

### Ver errores en tiempo real
```bash
# Logs de Laravel
tail -f storage/logs/laravel.log

# Logs de Docker
docker-compose logs -f app | grep -i error

# Ver queries SQL (agregar al .env)
DB_LOG=true
```

### Reset completo
```bash
# Borrar todo y empezar de cero
docker-compose down -v
docker-compose up -d --build
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

### Permisos
```bash
# Arreglar permisos de storage (si hay errores de escritura)
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

---

## 📊 MONITOREO

### Estado de contenedores
```bash
docker-compose ps                    # Ver estado
docker stats                         # Uso de recursos
docker-compose exec app php -v       # Versión PHP
docker-compose exec db mysql --version # Versión MySQL
```

### Backup de BD
```bash
# Exportar BD
docker-compose exec db mysqldump -u root -prootsecret dbventaslaravel > backup.sql

# Importar BD
docker-compose exec -T db mysql -u root -prootsecret dbventaslaravel < backup.sql
```

---

## ⚠️ PROBLEMAS COMUNES

### Error: "Connection refused" en BD
- Verificar que el contenedor db esté healthy: `docker-compose ps`
- Esperar 30s después de `docker-compose up` (MySQL tarda en iniciar)

### Error: "Class not found" después de composer install
```bash
docker-compose exec app composer dump-autoload
```

### Error: "Permission denied" en storage
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Assets no cargan (404 en CSS/JS)
```bash
docker-compose exec app npm run production
docker-compose exec app php artisan vendor:publish --tag=public --force
```

### Cambios en .env no se aplican
```bash
docker-compose exec app php artisan config:clear
docker-compose restart app
```

# Guía de Despliegue en Producción - Dokploy

## 📋 Tabla de Contenidos

1. [Requisitos](#requisitos)
2. [Configuración Inicial](#configuración-inicial)
3. [Despliegue en Dokploy](#despliegue-en-dokploy)
4. [Verificación](#verificación)
5. [Mantenimiento](#mantenimiento)
6. [Troubleshooting](#troubleshooting)

---

## 🔧 Requisitos

- Servidor con Dokploy instalado
- Dominio configurado: `andyland-sisventas.ndeapp.com`
- Certificado SSL (Let's Encrypt automático con Dokploy)
- Docker y Docker Compose instalados en el servidor

---

## 🚀 Configuración Inicial

### Paso 1: Clonar el Repositorio

```bash
cd /opt/dokploy/applications
git clone git@github.com:rootpy-90/andyland-sisventas.git
cd andyland-sisventas
```

### Paso 2: Configurar Variables de Entorno

```bash
# Copiar el ejemplo
cp .env.production.example .env

# Editar el archivo .env
nano .env
```

**Variables obligatorias a configurar:**

```env
# ============================================
# OBLIGATORIAS
# ============================================

# Generar con: php artisan key:generate --show
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

# Contraseñas seguras para la base de datos
DB_PASSWORD=TuPasswordSuperSeguro123!
DB_ROOT_PASSWORD=TuPasswordRootSuperSeguro456!

# ============================================
# OPCIONALES (valores por defecto recomendados)
# ============================================
APP_NAME="Andyland SisVentas"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://andyland-sisventas.ndeapp.com

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=dbventaslaravel
DB_USERNAME=sisventas

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync
```

### Paso 3: Generar APP_KEY

Si no tienes `php artisan` disponible localmente, puedes generar la key con:

```bash
# Opción 1: Usando Docker (recomendado)
docker run --rm -v $(pwd):/app -w /app php:7.4-cli php -r "echo 'base64:'.base64_encode(random_bytes(32));"

# Opción 2: Usando OpenSSL
echo "base64:$(openssl rand -base64 32)"
```

Copia el resultado y pégalo en `APP_KEY` del archivo `.env`.

### Paso 4: Hacer Commit de los Cambios

```bash
git add .env
git commit -m "chore: configurar variables de entorno para producción"
git push origin main
```

**⚠️ IMPORTANTE:** Nunca subas el archivo `.env` a un repositorio público. Si el repo es público, configura las variables directamente en Dokploy.

---

## 🎯 Despliegue en Dokploy

### Opción A: Despliegue desde GitHub (Recomendado)

1. **Acceder a Dokploy**
   - Ir a tu panel de Dokploy
   - Navegar a "Applications" → "New Application"

2. **Configurar la Aplicación**
   - **Name:** `andyland-sisventas`
   - **Repository:** Seleccionar `rootpy-90/andyland-sisventas`
   - **Branch:** `main`
   - **Build Method:** `Docker Compose`

3. **Configurar Docker Compose**
   - **Docker Compose Path:** `docker-compose.yml`
   - Dokploy detectará automáticamente los servicios `app` y `db`

4. **Configurar Variables de Entorno**
   - Ir a la pestaña "Environment"
   - Agregar las variables del archivo `.env`:
     ```
     APP_KEY=base64:...
     DB_PASSWORD=...
     DB_ROOT_PASSWORD=...
     ```

5. **Configurar Dominio**
   - Ir a la pestaña "Domains"
   - Agregar dominio: `andyland-sisventas.ndeapp.com`
   - Dokploy configurará automáticamente el SSL con Let's Encrypt

6. **Desplegar**
   - Click en "Deploy"
   - Esperar a que el build y deploy finalicen (5-10 minutos)

### Opción B: Despliegue Manual desde Servidor

```bash
# 1. Clonar repositorio
cd /opt/dokploy/applications
git clone git@github.com:rootpy-90/andyland-sisventas.git
cd andyland-sisventas

# 2. Configurar variables
cp .env.production.example .env
nano .env  # Editar con tus valores

# 3. Levantar servicios
docker-compose up -d --build

# 4. Verificar estado
docker-compose ps

# 5. Ver logs
docker-compose logs -f app
```

---

## ✅ Verificación

### 1. Verificar Estado de los Contenedores

```bash
docker-compose ps
```

**Salida esperada:**
```
NAME                      STATUS
andyland-sisventas-app    Up (healthy)
andyland-sisventas-db     Up (healthy)
```

### 2. Verificar Logs de la Aplicación

```bash
docker-compose logs app
```

**Buscar mensajes clave:**
```
=== Primera ejecución detectada ===
✓ MySQL conectado
Ejecutando migraciones...
Ejecutando seeders...
✓ Aplicación configurada correctamente
✓ Admin creado: admin@andyland.com / admin123
✓ Andyland SisVentas está listo
✓ URL: https://andyland-sisventas.ndeapp.com
```

### 3. Acceder a la Aplicación

- **URL:** https://andyland-sisventas.ndeapp.com
- **Admin:** admin@andyland.com / admin123
- **Cliente:** cliente@andyland.com / cliente123

### 4. Verificar Base de Datos

```bash
# Entrar al contenedor de MySQL
docker-compose exec db mysql -u root -p$DB_ROOT_PASSWORD dbventaslaravel

# Verificar tablas
SHOW TABLES;

# Verificar usuarios
SELECT id, name, email, idrol FROM users;

# Salir
exit
```

---

## 🔧 Mantenimiento

### Backup de Base de Datos

```bash
# Backup completo
docker-compose exec db mysqldump -u root -p$DB_ROOT_PASSWORD dbventaslaravel > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup comprimido
docker-compose exec db mysqldump -u root -p$DB_ROOT_PASSWORD dbventaslaravel | gzip > backup_$(date +%Y%m%d_%H%M%S).sql.gz
```

### Restaurar Backup

```bash
# Descomprimir si es necesario
gunzip backup_20260624_120000.sql.gz

# Restaurar
docker-compose exec -T db mysql -u root -p$DB_ROOT_PASSWORD dbventaslaravel < backup_20260624_120000.sql
```

### Actualizar Aplicación

```bash
# 1. Pull de últimos cambios
git pull origin main

# 2. Reconstruir y reiniciar
docker-compose up -d --build

# 3. Ejecutar migraciones (si las hay)
docker-compose exec app php artisan migrate --force

# 4. Limpiar caches
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Ver Uso de Recursos

```bash
# Uso de CPU y memoria
docker stats andyland-sisventas-app andyland-sisventas-db

# Uso de disco
docker system df
du -sh storage/
```

### Rotación de Logs

```bash
# Ver tamaño de logs
du -sh storage/logs/

# Limpiar logs antiguos
docker-compose exec app find /var/www/storage/logs -name "*.log" -mtime +30 -delete
```

---

## 🐛 Troubleshooting

### Error: "Connection refused" en MySQL

**Causa:** MySQL no está listo cuando la app intenta conectarse.

**Solución:**
```bash
# Verificar estado de MySQL
docker-compose ps db

# Ver logs de MySQL
docker-compose logs db

# Reiniciar servicios
docker-compose restart db app
```

### Error: "APP_KEY not set"

**Causa:** La variable `APP_KEY` no está configurada en `.env`.

**Solución:**
```bash
# Generar nueva key
docker-compose exec app php artisan key:generate

# Reiniciar app
docker-compose restart app
```

### Error: "Permission denied" en storage/

**Causa:** Permisos incorrectos en directorios de storage.

**Solución:**
```bash
docker-compose exec app chown -R www-data:www-data /var/www/storage
docker-compose exec app chmod -R 775 /var/www/storage
```

### Error: "502 Bad Gateway" en Dokploy

**Causa:** La app no está respondiendo o el healthcheck falla.

**Solución:**
```bash
# Ver logs de la app
docker-compose logs app

# Verificar que el contenedor esté healthy
docker inspect andyland-sisventas-app | grep -A 5 "Health"

# Reiniciar app
docker-compose restart app
```

### Error: "Migration failed"

**Causa:** Problema con migraciones de base de datos.

**Solución:**
```bash
# Ver estado de migraciones
docker-compose exec app php artisan migrate:status

# Revertir última migración
docker-compose exec app php artisan migrate:rollback

# Re-ejecutar migraciones
docker-compose exec app php artisan migrate --force
```

### Error: "Seeders failed"

**Causa:** Problema al ejecutar seeders.

**Solución:**
```bash
# Ejecutar seeders manualmente con verbose
docker-compose exec app php artisan db:seed --force --verbose

# Verificar que las tablas existan
docker-compose exec db mysql -u root -p$DB_ROOT_PASSWORD dbventaslaravel -e "SHOW TABLES;"
```

### Error: "SSL Certificate not valid"

**Causa:** Let's Encrypt no pudo renovar el certificado.

**Solución:**
```bash
# En Dokploy, ir a la configuración del dominio y forzar renovación
# O manualmente:
docker-compose exec app certbot renew
```

### Error: "Out of memory"

**Causa:** La app o la BD están usando más memoria de la asignada.

**Solución:**
```bash
# Aumentar límites en docker-compose.yml
# Editar deploy.resources.limits.memory
# Luego:
docker-compose up -d --build
```

---

## 📊 Monitoreo

### Logs en Tiempo Real

```bash
# Todos los servicios
docker-compose logs -f

# Solo la app
docker-compose logs -f app

# Solo la BD
docker-compose logs -f db

# Últimas 100 líneas
docker-compose logs --tail=100 app
```

### Métricas de Rendimiento

```bash
# Estadísticas de contenedores
docker stats

# Procesos dentro del contenedor
docker-compose exec app ps aux

# Conexiones activas a MySQL
docker-compose exec db mysql -u root -p$DB_ROOT_PASSWORD -e "SHOW PROCESSLIST;"
```

### Health Checks

```bash
# Ver estado de health
docker inspect --format='{{.State.Health.Status}}' andyland-sisventas-app

# Ver detalles del health check
docker inspect --format='{{json .State.Health}}' andyland-sisventas-app | jq
```

---

## 🔒 Seguridad

### Checklist de Seguridad

- [ ] `APP_DEBUG=false` en `.env`
- [ ] `APP_ENV=production` en `.env`
- [ ] Contraseñas fuertes en `DB_PASSWORD` y `DB_ROOT_PASSWORD`
- [ ] SSL/TLS habilitado (automático con Dokploy)
- [ ] Firewall configurado (solo puertos 80, 443, 22)
- [ ] Backups automáticos configurados
- [ ] Logs monitoreados
- [ ] Actualizaciones de seguridad aplicadas

### Cambiar Contraseñas

```bash
# 1. Editar .env
nano .env

# Cambiar DB_PASSWORD y DB_ROOT_PASSWORD

# 2. Recrear contenedores
docker-compose down
docker-compose up -d

# 3. Actualizar contraseña en MySQL
docker-compose exec db mysql -u root -p$DB_ROOT_PASSWORD
ALTER USER 'sisventas'@'%' IDENTIFIED BY 'NuevaPassword123!';
FLUSH PRIVILEGES;
exit
```

---

## 📞 Soporte

Si encuentras problemas:

1. Revisar logs: `docker-compose logs app`
2. Verificar estado: `docker-compose ps`
3. Consultar documentación: `docs/documentacion.md`
4. Revisar troubleshooting en esta guía

---

**Última actualización:** 24 de Junio de 2026

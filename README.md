# Andyland SisVentas - Sistema de Gestión de Ventas con E-commerce

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-5.4-red.svg" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-7.4-blue.svg" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-5.7-green.svg" alt="MySQL">
  <img src="https://img.shields.io/badge/Docker-Ready-orange.svg" alt="Docker">
  <img src="https://img.shields.io/badge/License-MIT-lightgrey.svg" alt="License">
</p>

## 📋 Tabla de Contenidos

- [Descripción](#-descripción)
- [Características](#-características)
- [Tecnologías](#-tecnologías)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Uso](#-uso)
- [Estructura del Proyecto](#-estructura-del-proyecto)
- [Documentación](#-documentación)
- [Créditos](#-créditos)

---

## 📖 Descripción

**Andyland SisVentas** es un sistema integral de gestión de ventas desarrollado como proyecto de tesis, diseñado para pequeñas y medianas empresas. Combina un panel administrativo completo con una tienda online (e-commerce) para gestionar todo el ciclo comercial: desde la compra de mercadería hasta la venta al cliente final.

### ¿Qué hace este sistema?

- **Gestión de Inventario**: Control de artículos, categorías, stock y movimientos
- **Ventas**: Registro de ventas físicas y online con estados (Pendiente, Aprobado, Cancelado)
- **Compras**: Registro de ingresos de mercadería desde proveedores
- **E-commerce**: Tienda online con carrito de compras, checkout y seguimiento de pedidos
- **Clientes**: Gestión de perfiles, historial de compras y categorías automáticas
- **Reportes**: Estadísticas de ventas, productos más vendidos, ingresos mensuales
- **Seguridad**: Autenticación, roles, permisos y soft delete de cuentas

---

## ✨ Características

### Panel Administrativo
- ✅ Dashboard con métricas en tiempo real
- ✅ Gestión completa de inventario (artículos, categorías, stock)
- ✅ Control de clientes y proveedores
- ✅ Registro de compras y ventas
- ✅ Anulación de ventas con devolución automática de stock
- ✅ Gestión de usuarios con roles (Administrador, Cliente)
- ✅ Reportes detallados con gráficos
- ✅ Control de caja y arqueos
- ✅ Gestión de fechas de entrega

### Tienda Online (E-commerce)
- ✅ Catálogo de productos con búsqueda y filtros
- ✅ Carrito de compras persistente (localStorage)
- ✅ Checkout con datos de envío
- ✅ Seguimiento de pedidos en tiempo real
- ✅ Perfil de usuario con historial completo
- ✅ Exportación de datos personales (GDPR)
- ✅ Soft delete de cuentas (reversible 30 días)
- ✅ Categorías automáticas de clientes (Nueva, Regular, Frecuente, VIP)

### Características Técnicas
- ✅ Dockerizado para desarrollo consistente
- ✅ Relaciones Eloquent en todos los modelos
- ✅ Queries optimizadas (sin N+1)
- ✅ API REST para consumo externo
- ✅ Responsive design (AdminLTE + Bootstrap)
- ✅ Migraciones y seeders para BD
- ✅ Documentación técnica completa

---

## 🛠 Tecnologías

| Componente | Tecnología | Versión |
|------------|-----------|---------|
| **Backend** | Laravel | 5.4 |
| **Lenguaje** | PHP | 7.4 |
| **Base de Datos** | MySQL | 5.7 |
| **Frontend** | Blade + AdminLTE | 2.4.18 |
| **JavaScript** | jQuery | 3.x |
| **Containerización** | Docker + Docker Compose | 20.10+ |

---

## 📦 Requisitos

### Para Desarrollo (con Docker)
- Docker >= 20.10
- Docker Compose >= 3.8
- Git

### Para Desarrollo (sin Docker)
- PHP >= 7.4 con extensiones:
  - pdo_mysql
  - mbstring
  - openssl
  - tokenizer
  - xml
  - json
  - gd
  - zip
  - bcmath
  - curl
- Composer >= 2.0
- Node.js >= 16.x
- npm >= 8.x
- MySQL >= 5.7

### Para Producción
- Servidor web: Apache 2.4+ o Nginx 1.18+
- PHP >= 7.4
- MySQL >= 5.7
- SSL/TLS certificado (Let's Encrypt recomendado)
- 2GB RAM mínimo
- 20GB disco mínimo

---

## 🚀 Instalación

### Opción 1: Instalación con Docker (Recomendado)

#### Paso 1: Clonar el repositorio
```bash
git clone git@github.com:rootpy-90/andyland-sisventas.git
cd andyland-sisventas
```

#### Paso 2: Configurar variables de entorno
```bash
cp .env.example .env
```

Editar `.env` con los valores por defecto:
```env
APP_NAME="Andyland PY - SisVentas"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=dbventaslaravel
DB_USERNAME=sisventas
DB_PASSWORD=secret
```

#### Paso 3: Levantar contenedores
```bash
# Construir y levantar servicios
docker-compose up -d --build

# Verificar estado
docker-compose ps
```

**Salida esperada:**
```
NAME            IMAGE                    STATUS
sisventas-app   andyland-sisventas-app   Up (healthy)
sisventas-db    mysql:5.7                Up (healthy)
```

#### Paso 4: Configurar aplicación
```bash
# Generar APP_KEY
docker-compose exec app php artisan key:generate

# Ejecutar migraciones
docker-compose exec app php artisan migrate --force

# Poblar base de datos con datos de prueba (opcional)
docker-compose exec app php artisan db:seed --force
```

#### Paso 5: Acceder a la aplicación
- **Aplicación:** http://localhost:8000
- **phpMyAdmin:** http://localhost:8081 (levantar con `docker-compose --profile tools up -d`)

**Usuarios de prueba:**
- Admin: `admin@andyland.com` / `admin123`
- Cliente: `cliente@andyland.com` / `cliente123`

#### Comandos útiles
```bash
# Ver logs en tiempo real
docker-compose logs -f app

# Entrar al contenedor
docker-compose exec app bash

# Dentro del contenedor:
php artisan migrate          # Ejecutar migraciones
php artisan db:seed          # Poblar BD
php artisan tinker           # Console interactivo
php artisan route:list       # Ver rutas
composer install             # Instalar dependencias
npm install && npm run dev   # Compilar assets

# Detener servicios
docker-compose down

# Reset completo (borra BD)
docker-compose down -v
docker-compose up -d --build
```

---

### Opción 2: Instalación Local (sin Docker)

#### Paso 1: Clonar el repositorio
```bash
git clone git@github.com:rootpy-90/andyland-sisventas.git
cd andyland-sisventas
```

#### Paso 2: Instalar dependencias
```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

#### Paso 3: Configurar .env
```bash
cp .env.example .env
```

Editar `.env`:
```env
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=dbventaslaravel
DB_USERNAME=root
DB_PASSWORD=
```

#### Paso 4: Configurar aplicación
```bash
# Generar APP_KEY
php artisan key:generate

# Crear base de datos
mysql -u root -p -e "CREATE DATABASE dbventaslaravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Ejecutar migraciones
php artisan migrate

# Poblar datos (opcional)
php artisan db:seed
```

#### Paso 5: Compilar assets
```bash
npm run dev
```

#### Paso 6: Iniciar servidor
```bash
php artisan serve --port=8000
```

Acceder a: http://localhost:8000

---

### Opción 3: Despliegue en Producción

Ver guía completa en [docs/documentacion.md](docs/documentacion.md#84-despliegue-en-producción)

**Resumen rápido:**

1. Clonar repositorio en servidor
2. Instalar dependencias de producción: `composer install --optimize-autoloader --no-dev`
3. Configurar `.env` para producción (`APP_DEBUG=false`, `APP_ENV=production`)
4. Configurar Apache/Nginx con virtual host
5. Instalar SSL con Let's Encrypt
6. Configurar permisos: `chmod -R 775 storage bootstrap/cache`
7. Optimizar: `php artisan config:cache && php artisan route:cache`
8. Configurar backups automáticos

---

## 💻 Uso

### Panel Administrativo

1. Iniciar sesión como administrador: `admin@andyland.com` / `admin123`
2. Acceder a http://localhost:8000/home
3. Navegar por el menú lateral:
   - **Dashboard**: Métricas y estadísticas
   - **Almacén**: Gestión de artículos y categorías
   - **Ventas**: Registro y seguimiento de ventas
   - **Compras**: Registro de ingresos de mercadería
   - **Clientes**: Gestión de clientes
   - **Proveedores**: Gestión de proveedores
   - **Reportes**: Estadísticas detalladas

### Tienda Online

1. Navegar como visitante: http://localhost:8000/tienda
2. Agregar productos al carrito
3. Iniciar sesión o registrarse
4. Ir a checkout y confirmar pedido
5. Admin aprueba pedido en panel
6. Cliente puede ver estado en "Mis Compras"

### Cliente Registrado

1. Iniciar sesión: `cliente@andyland.com` / `cliente123`
2. Acceder a http://localhost:8000/tienda/perfil
3. Funcionalidades:
   - Editar datos personales
   - Cambiar contraseña
   - Cambiar email
   - Ver historial de pedidos
   - Exportar datos personales (JSON)
   - Desactivar cuenta (soft delete)

---

## 📁 Estructura del Proyecto

```
andyland-sisventas/
├── app/                          # Lógica de la aplicación
│   ├── Http/
│   │   ├── Controllers/          # Controladores
│   │   │   ├── Api/              # Controladores API REST
│   │   │   ├── Auth/             # Autenticación
│   │   │   └── *.php             # Controladores web
│   │   ├── Middleware/           # Middleware (auth, isAdmin)
│   │   └── Requests/             # Validaciones
│   ├── *.php                     # Modelos Eloquent
│   └── User.php                  # Modelo de usuario
├── config/                       # Configuración
├── database/
│   ├── migrations/               # Migraciones de BD
│   ├── seeds/                    # Seeders
│   └── factories/                # Factories para testing
├── docs/
│   └── documentacion.md          # Documentación técnica completa
├── public/                       # Archivos públicos
├── resources/
│   └── views/                    # Plantillas Blade
│       ├── layouts/              # Layouts principales
│       ├── admin/                # Vistas de administración
│       └── tienda/               # Vistas de tienda online
├── routes/
│   ├── web.php                   # Rutas web
│   └── api.php                   # Rutas API
├── storage/                      # Logs, cache, uploads
├── tests/                        # Tests unitarios
├── docker/
│   └── mysql/
│       └── init.sql              # Script de inicialización BD
├── docker-compose.yml            # Orquestación Docker
├── Dockerfile                    # Build de imagen PHP
├── .env.example                  # Variables de entorno ejemplo
├── COMANDOS.md                   # Guía de comandos útiles
└── composer.json                 # Dependencias PHP
```

---

## 📚 Documentación

La documentación técnica completa está disponible en [docs/documentacion.md](docs/documentacion.md)

**Contenido:**
1. Descripción General del Proyecto
2. Tecnologías Utilizadas
3. Arquitectura del Sistema
4. Base de Datos (diagramas, tablas, relaciones)
5. Módulos del Sistema (11 módulos detallados)
6. API REST
7. Seguridad y Autenticación
8. Instalación y Despliegue
9. Anexos

---

## 🔒 Seguridad

### Características de Seguridad Implementadas
- ✅ Encriptación de contraseñas con bcrypt
- ✅ Protección CSRF en todos los formularios
- ✅ Middleware de autenticación y autorización
- ✅ Soft delete de cuentas (reversible)
- ✅ Validación de datos en formularios
- ✅ Eloquent ORM (previene SQL injection)
- ✅ Logout seguro con POST (previene CSRF logout)

### Recomendaciones para Producción
- Habilitar HTTPS/SSL
- Configurar rate limiting en API
- Implementar 2FA para administradores
- Configurar backups automáticos
- Monitorear logs de seguridad
- Mantener dependencias actualizadas

---

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# O con PHPUnit
./vendor/bin/phpunit
```

---

## 📊 Base de Datos

### Tablas Principales
- `users` - Usuarios del sistema
- `persona` - Personas (clientes, proveedores, administradores)
- `articulo` - Catálogo de productos
- `categoria` - Categorías de artículos
- `venta` - Registro de ventas
- `detalle_venta` - Detalle de artículos en ventas
- `ingreso` - Registro de compras
- `detalle_ingreso` - Detalle de artículos en compras
- `roles` - Roles de usuario
- `fechas_entrega` - Fechas de entrega
- `caja` - Control de caja

### Relaciones Eloquent
Todos los modelos tienen relaciones Eloquent definidas:
- User ↔ Persona (OneToOne)
- Persona → Ventas, Ingresos (OneToMany)
- Categoria → Articulos (OneToMany)
- Articulo → Categoria, DetallesVenta, DetallesIngreso
- Venta → Cliente, Detalles (OneToMany)
- Ingreso → Proveedor, Detalles (OneToMany)

---

## 🤝 Contribuciones

Este proyecto fue desarrollado como proyecto de tesis. Las contribuciones son bienvenidas.

Para contribuir:
1. Fork el repositorio
2. Crear una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -m 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abrir un Pull Request

---

## 📄 Licencia

Este proyecto fue desarrollado con fines académicos como proyecto de tesis.

---

## 👥 Créditos

**Desarrollado por:** [Nombre del estudiante]

**Universidad:** [Nombre de la universidad]

**Carrera:** [Nombre de la carrera]

**Año:** 2026

**Tutor:** [Nombre del tutor]

---

## 📞 Contacto

**Email:** [email del estudiante]

**Repositorio:** https://github.com/rootpy-90/andyland-sisventas

---

## 🙏 Agradecimientos

- Laravel Framework
- AdminLTE
- Comunidad de desarrolladores
- Tutor y tribunal de tesis

---

<p align="center">
  <strong>Andyland SisVentas</strong> - Sistema de Gestión de Ventas con E-commerce<br>
  Proyecto de Tesis 2026
</p>

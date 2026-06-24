# Documentación Técnica Completa - Andyland SisVentas

## Tabla de Contenidos

1. [Descripción General del Proyecto](#1-descripción-general-del-proyecto)
2. [Tecnologías Utilizadas](#2-tecnologías-utilizadas)
3. [Arquitectura del Sistema](#3-arquitectura-del-sistema)
4. [Base de Datos](#4-base-de-datos)
5. [Módulos del Sistema](#5-módulos-del-sistema)
6. [API REST](#6-api-rest)
7. [Seguridad y Autenticación](#7-seguridad-y-autenticación)
8. [Instalación y Despliegue](#8-instalación-y-despliegue)

---

## 1. Descripción General del Proyecto

### 1.1 ¿Qué es Andyland SisVentas?

**Andyland SisVentas** es un sistema de gestión de ventas integral desarrollado como proyecto de tesis, diseñado para pequeñas y medianas empresas que necesitan controlar su operación comercial desde la compra de mercadería hasta la venta al cliente final, incluyendo un módulo de e-commerce para ventas online.

### 1.2 Propósito y Objetivos

El sistema tiene como propósito principal:

- **Gestionar inventario**: Control de artículos, categorías, stock y movimientos
- **Administrar ventas**: Registro de ventas, pedidos, estados y comprobantes
- **Control de compras**: Gestión de proveedores e ingresos de mercadería
- **E-commerce**: Tienda online con carrito de compras y checkout
- **Gestión de usuarios**: Roles, permisos y perfiles de cliente
- **Reportes**: Estadísticas de ventas, productos y clientes

### 1.3 Alcance del Sistema

El sistema cubre dos áreas principales:

**Panel Administrativo** (acceso restringido a administradores):
- Dashboard con métricas en tiempo real
- Gestión de inventario (artículos, categorías, stock)
- Gestión de clientes y proveedores
- Registro de compras y ventas
- Control de caja y arqueos
- Gestión de usuarios y roles
- Reportes y estadísticas
- Gestión de fechas de entrega

**Tienda Online** (acceso público + clientes registrados):
- Catálogo de productos con búsqueda y filtros
- Carrito de compras persistente (localStorage)
- Checkout con datos de envío
- Seguimiento de pedidos
- Perfil de usuario con historial
- Exportación de datos personales (GDPR)

### 1.4 Público Objetivo

- **Administradores**: Personal de la empresa que gestiona el negocio
- **Clientes**: Personas que compran en la tienda física u online
- **Proveedores**: Entidades que suministran mercadería (registrados en el sistema)

---

## 2. Tecnologías Utilizadas

### 2.1 Stack Tecnológico

| Componente | Tecnología | Versión | Propósito |
|------------|-----------|---------|-----------|
| **Backend** | Laravel | 5.4 | Framework PHP MVC |
| **Lenguaje** | PHP | 7.4 | Lenguaje de programación |
| **Base de Datos** | MySQL | 5.7 | Sistema de gestión de bases de datos relacional |
| **Frontend** | Blade | 5.4 | Motor de plantillas de Laravel |
| **CSS Framework** | AdminLTE | 2.4.18 | Framework CSS para panel admin |
| **JavaScript** | jQuery | 3.x | Manipulación DOM y AJAX |
| **Iconos** | Font Awesome | 4.7 | Iconografía |
| **Containerización** | Docker | 20.10+ | Containerización de servicios |
| **Orquestación** | Docker Compose | 3.8 | Orquestación de contenedores |

### 2.2 Dependencias PHP (composer.json)

```json
{
  "require": {
    "php": ">=7.4",
    "laravel/framework": "5.4.*",
    "laravel/tinker": "~1.0",
    "laravelcollective/html": "^5.4.0"
  },
  "require-dev": {
    "fzaninotto/faker": "~1.4",
    "mockery/mockery": "0.9.*",
    "phpunit/phpunit": "~5.7"
  }
}
```

**Descripción de dependencias:**

- **laravel/framework**: Core del framework Laravel 5.4
- **laravel/tinker**: REPL interactivo para Laravel (testing y debugging)
- **laravelcollective/html**: Helpers HTML para formularios (obsoleto, mantenido por compatibilidad)
- **fzaninotto/faker**: Generador de datos falsos para testing
- **mockery/mockery**: Framework de mocking para tests
- **phpunit/phpunit**: Framework de testing unitario

### 2.3 Dependencias JavaScript (package.json)

```json
{
  "devDependencies": {
    "axios": "^0.18.0",
    "bootstrap-sass": "^3.3.7",
    "cross-env": "^5.1",
    "jquery": "^3.2",
    "laravel-mix": "^2.0",
    "lodash": "^4.17.4",
    "vue": "^2.5.7"
  }
}
```

**Descripción:**

- **axios**: Cliente HTTP para peticiones AJAX
- **bootstrap-sass**: Framework CSS (usado parcialmente)
- **jquery**: Biblioteca JavaScript para manipulación DOM
- **laravel-mix**: Herramienta de compilación de assets (webpack wrapper)
- **lodash**: Biblioteca de utilidades JavaScript
- **vue**: Framework JavaScript reactivo (no utilizado activamente)

### 2.4 Extensiones PHP Requeridas

- **pdo_mysql**: Conexión a MySQL
- **mbstring**: Soporte multibyte para strings
- **openssl**: Encriptación y seguridad
- **tokenizer**: Análisis de código PHP
- **xml**: Procesamiento XML
- **json**: Serialización JSON
- **gd**: Procesamiento de imágenes
- **zip**: Compresión/descompresión
- **bcmath**: Operaciones matemáticas de precisión
- **curl**: Peticiones HTTP

### 2.5 Herramientas de Desarrollo

- **Docker**: Containerización para entorno de desarrollo consistente
- **Docker Compose**: Orquestación de servicios (app, db, phpmyadmin)
- **Composer**: Gestor de dependencias PHP
- **npm**: Gestor de paquetes JavaScript
- **Git**: Control de versiones
- **phpMyAdmin**: Administración web de MySQL (opcional)

---

## 3. Arquitectura del Sistema

### 3.1 Patrón de Diseño: MVC (Model-View-Controller)

El sistema sigue el patrón **Modelo-Vista-Controlador** de Laravel:

```
┌─────────────────────────────────────────────────────────────┐
│                        CLIENTE                               │
│              (Navegador Web / Mobile)                        │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTP Request
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                     ROUTES (web.php)                         │
│         Definición de rutas y middleware                     │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                   MIDDLEWARE                                 │
│    auth, isAdmin, VerifyCsrfToken, etc.                     │
└────────────────────────┬────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                  CONTROLLERS                                 │
│   Lógica de negocio, validaciones, autorización             │
│   (ArticuloController, VentaController, etc.)               │
└────────────────────────┬────────────────────────────────────┘
                         │
              ┌──────────┴──────────┐
              ▼                     ▼
┌─────────────────────┐   ┌─────────────────────┐
│      MODELS         │   │      VIEWS          │
│  Lógica de datos,   │   │  Plantillas Blade   │
│  relaciones Eloquent│   │  HTML + PHP         │
│  (Articulo, Venta)  │   │  (admin/, tienda/)  │
└─────────────────────┘   └─────────────────────┘
              │                     │
              ▼                     ▼
┌─────────────────────────────────────────────────────────────┐
│                    DATABASE (MySQL)                          │
│              Tablas: articulo, venta, persona, etc.         │
└─────────────────────────────────────────────────────────────┘
```

### 3.2 Estructura de Directorios

```
andyland-sisventas/
├── app/                          # Lógica de la aplicación
│   ├── Http/
│   │   ├── Controllers/          # Controladores (lógica de negocio)
│   │   │   ├── Api/              # Controladores API REST
│   │   │   ├── Auth/             # Controladores de autenticación
│   │   │   └── *.php             # Controladores web
│   │   ├── Middleware/           # Middleware (auth, isAdmin)
│   │   └── Requests/             # Validaciones de formularios
│   ├── *.php                     # Modelos Eloquent
│   └── User.php                  # Modelo de usuario
├── bootstrap/                    # Archivos de bootstrap de Laravel
├── config/                       # Configuración de la aplicación
│   ├── app.php                   # Config general
│   ├── database.php              # Config de base de datos
│   ├── auth.php                  # Config de autenticación
│   └── *.php                     # Otros configs
├── database/
│   ├── migrations/               # Migraciones de BD
│   ├── seeds/                    # Seeders de datos
│   └── factories/                # Factories para testing
├── public/                       # Archivos públicos (document root)
│   ├── index.php                 # Punto de entrada
│   ├── css/                      # CSS compilado
│   ├── js/                       # JS compilado
│   ├── imagenes/                 # Imágenes de artículos
│   └── comprobantes/             # Comprobantes de pago
├── resources/
│   ├── views/                    # Plantillas Blade
│   │   ├── layouts/              # Layouts principales
│   │   │   ├── admin.blade.php   # Layout panel admin
│   │   │   ├── tienda.blade.php  # Layout tienda online
│   │   │   └── app.blade.php     # Layout genérico
│   │   ├── admin/                # Vistas de administración
│   │   ├── tienda/               # Vistas de tienda online
│   │   ├── ventas/               # Vistas de ventas
│   │   ├── compras/              # Vistas de compras
│   │   └── auth/                 # Vistas de autenticación
│   └── assets/                   # Assets sin compilar
├── routes/
│   ├── web.php                   # Rutas web
│   └── api.php                   # Rutas API
├── storage/
│   ├── app/                      # Archivos generados
│   ├── framework/                # Cache, sessions, views
│   └── logs/                     # Logs de la aplicación
├── tests/                        # Tests unitarios y feature
├── vendor/                       # Dependencias Composer
├── docker/
│   └── mysql/
│       └── init.sql              # Script de inicialización BD
├── docker-compose.yml            # Orquestación Docker
├── Dockerfile                    # Build de imagen PHP
├── .env.example                  # Variables de entorno ejemplo
└── composer.json                 # Dependencias PHP
```

### 3.3 Flujo de Datos

#### 3.3.1 Flujo de Venta (Admin)

```
1. Admin crea venta en /ventas/venta/create
2. Controller valida datos y stock
3. Se crea registro en tabla 'venta' (estado='A')
4. Se crean registros en 'detalle_venta' por cada artículo
5. Se descuenta stock en tabla 'articulo'
6. Se genera comprobante PDF
```

#### 3.3.2 Flujo de Pedido (Tienda Online)

```
1. Cliente agrega productos al carrito (localStorage)
2. Cliente va a /tienda/checkout
3. Si no está logueado, redirige a /login
4. Cliente confirma pedido con datos de envío
5. Controller crea venta (estado='P', pendiente)
6. Admin aprueba pedido en /ventas/venta (cambia estado a 'A')
7. Cliente recibe notificación y puede ver en /tienda/mis-compras
```

#### 3.3.3 Flujo de Compra (Ingreso de Mercadería)

```
1. Admin registra compra en /compras/ingreso/create
2. Selecciona proveedor y artículos
3. Controller crea registro en 'ingreso' (estado='A')
4. Se crean registros en 'detalle_ingreso'
5. Se actualiza stock en 'articulo' (incrementa)
6. Se registra precio de compra y venta
```

### 3.4 Namespace y Autoloading

El proyecto usa el namespace **`sisVentas`** (no el estándar `App`):

```php
namespace sisVentas;

use Illuminate\Database\Eloquent\Model;

class Articulo extends Model
{
    // ...
}
```

**Configuración en composer.json:**

```json
"autoload": {
    "psr-4": {
        "sisVentas\\": "app/"
    }
}
```

---

## 4. Base de Datos

### 4.1 Diagrama Entidad-Relación

```
┌─────────────────┐
│     users       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email           │
│ password        │
│ idrol (FK)      │─────┐
│ idpersona (FK)  │──┐  │
│ deleted_at      │  │  │
└─────────────────┘  │  │
                     │  │
                     │  │  ┌─────────────────┐
                     │  └─▶│    persona      │
                     │     ├─────────────────┤
                     │     │ idpersona (PK)  │
                     │     │ tipo_persona    │
                     │     │ nombre          │
                     │     │ apellido        │
                     │     │ email           │
                     │     │ telefono        │
                     │     │ direccion       │
                     │     │ deleted_at      │
                     │     └────────┬────────┘
                     │              │
                     │              │ 1:N (como cliente)
                     │              ▼
                     │     ┌─────────────────┐
                     │     │     venta       │
                     │     ├─────────────────┤
                     │     │ idventa (PK)    │
                     │     │ idcliente (FK)  │
                     │     │ tipo_comprobante│
                     │     │ num_comprobante │
                     │     │ fecha_hora      │
                     │     │ total_venta     │
                     │     │ estado          │
                     │     └────────┬────────┘
                     │              │
                     │              │ 1:N
                     │              ▼
                     │     ┌─────────────────┐
                     │     │  detalle_venta  │
                     │     ├─────────────────┤
                     │     │iddetalle_venta  │
                     │     │ idventa (FK)    │
                     │     │ idarticulo (FK) │──┐
                     │     │ cantidad        │  │
                     │     │ precio_venta    │  │
                     │     └─────────────────┘  │
                     │                          │
                     │                          │ N:1
                     │                          ▼
                     │                 ┌─────────────────┐
                     │                 │    articulo     │
                     │                 ├─────────────────┤
                     │                 │idarticulo (PK)  │
                     │                 │idcategoria (FK) │──┐
                     │                 │ codigo          │  │
                     │                 │ nombre          │  │
                     │                 │ stock           │  │
                     │                 │ precio          │  │
                     │                 └─────────────────┘  │
                     │                                      │
                     │                                      │ N:1
                     │                                      ▼
                     │                             ┌─────────────────┐
                     │                             │   categoria     │
                     │                             ├─────────────────┘
                     │                             │idcategoria (PK) │
                     │                             │ nombre          │
                     │                             │ descripcion     │
                     │                             └─────────────────┘
                     │
                     │  ┌─────────────────┐
                     └─▶│     roles       │
                        ├─────────────────┤
                        │ idrol (PK)      │
                        │ nombre          │
                        │ es_admin        │
                        └─────────────────┘
```

### 4.2 Tablas del Sistema

#### 4.2.1 Tabla: `users`

**Propósito**: Almacenar credenciales de acceso al sistema

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `name` | VARCHAR(191) | NO | - | Nombre de usuario |
| `email` | VARCHAR(191) | NO | - | Correo electrónico (único) |
| `password` | VARCHAR(191) | NO | - | Contraseña encriptada (bcrypt) |
| `remember_token` | VARCHAR(100) | YES | NULL | Token "Recordarme" |
| `created_at` | TIMESTAMP | YES | NULL | Fecha de creación |
| `updated_at` | TIMESTAMP | YES | NULL | Fecha de última actualización |
| `idrol` | INT | NO | 2 | FK a tabla roles (1=Admin, 2=Cliente) |
| `idpersona` | INT UNSIGNED | YES | NULL | FK a tabla persona |
| `deleted_at` | TIMESTAMP | YES | NULL | Soft delete (fecha de eliminación) |

**Índices:**
- PRIMARY KEY (`id`)
- UNIQUE (`email`)

**Relaciones:**
- `idrol` → `roles.idrol` (ManyToOne)
- `idpersona` → `persona.idpersona` (OneToOne)

**Notas:**
- La contraseña se almacena usando `Hash::make()` (bcrypt)
- `idrol` determina el nivel de acceso (1=Administrador, 2=Cliente)
- `deleted_at` se usa para soft delete (cuenta desactivada pero no eliminada físicamente)

---

#### 4.2.2 Tabla: `persona`

**Propósito**: Almacenar datos de personas (clientes, proveedores, administradores)

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `idpersona` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `tipo_persona` | VARCHAR(20) | NO | 'Cliente' | Tipo: Cliente, Proveedor, Administrador |
| `nombre` | VARCHAR(100) | NO | - | Nombre |
| `apellido` | VARCHAR(100) | YES | NULL | Apellido |
| `tipo_documento` | VARCHAR(20) | YES | NULL | Tipo: DNI, RUC, PAS |
| `num_documento` | VARCHAR(20) | YES | NULL | Número de documento |
| `direccion` | VARCHAR(255) | YES | NULL | Dirección completa |
| `telefono` | VARCHAR(20) | YES | NULL | Teléfono / WhatsApp |
| `email` | VARCHAR(100) | YES | NULL | Correo electrónico |
| `ciudad` | VARCHAR(100) | YES | NULL | Ciudad |
| `barrio` | VARCHAR(100) | YES | NULL | Barrio |
| `pais` | VARCHAR(50) | YES | NULL | País |
| `referencia` | TEXT | YES | NULL | Referencia de ubicación |
| `deleted_at` | TIMESTAMP | YES | NULL | Soft delete |
| `categoria` | VARCHAR(50) | YES | 'Nueva' | Categoría de cliente (calculada) |

**Índices:**
- PRIMARY KEY (`idpersona`)
- INDEX (`tipo_persona`)
- INDEX (`email`)

**Relaciones:**
- OneToOne con `users` (cuando es usuario del sistema)
- OneToMany con `venta` (como cliente)
- OneToMany con `ingreso` (como proveedor)

**Notas:**
- `tipo_persona` clasifica a la persona: Cliente, Proveedor, Administrador, Natural
- `categoria` se calcula automáticamente según historial de compras (Nueva, Regular, Frecuente, VIP)
- `deleted_at` permite soft delete (preserva historial de ventas)

---

#### 4.2.3 Tabla: `articulo`

**Propósito**: Catálogo de productos disponibles para venta

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `idarticulo` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `idcategoria` | INT UNSIGNED | NO | - | FK a tabla categoria |
| `codigo` | VARCHAR(50) | YES | NULL | Código interno del artículo |
| `nombre` | VARCHAR(200) | NO | - | Nombre del artículo |
| `stock` | INT | NO | 0 | Cantidad disponible |
| `descripcion` | TEXT | YES | NULL | Descripción detallada |
| `imagen` | VARCHAR(255) | YES | NULL | Ruta de imagen (relative path) |
| `estado` | VARCHAR(20) | NO | 'Activo' | Estado: Activo, Inactivo |
| `tiempo_entrega` | INT | YES | 0 | Días estimados de entrega |

**Índices:**
- PRIMARY KEY (`idarticulo`)
- INDEX (`estado`)
- INDEX (`stock`)
- FOREIGN KEY (`idcategoria`) → `categoria.idcategoria`

**Relaciones:**
- ManyToOne con `categoria`
- OneToMany con `detalle_venta`
- OneToMany con `detalle_ingreso`

**Notas:**
- `stock` se actualiza automáticamente con ventas (decrementa) y compras (incrementa)
- `imagen` almacena ruta relativa (ej: `imagenes/articulos/producto.jpg`)
- `tiempo_entrega` se usa en tienda online para estimar fechas de entrega
- **NO tiene campo `precio`**: el precio se obtiene del último `detalle_ingreso`

---

#### 4.2.4 Tabla: `categoria`

**Propósito**: Clasificación de artículos

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `idcategoria` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `nombre` | VARCHAR(100) | NO | - | Nombre de la categoría |
| `descripcion` | TEXT | YES | NULL | Descripción |
| `condicion` | TINYINT(1) | NO | 1 | Estado: 1=Activa, 0=Inactiva |

**Índices:**
- PRIMARY KEY (`idcategoria`)

**Relaciones:**
- OneToMany con `articulo`

---

#### 4.2.5 Tabla: `venta`

**Propósito**: Registro de ventas realizadas (físicas y online)

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `idventa` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `idcliente` | INT UNSIGNED | NO | - | FK a tabla persona |
| `tipo_comprobante` | VARCHAR(20) | NO | - | Tipo: Ticket, Factura, Boleta |
| `serie_comprobante` | VARCHAR(20) | YES | NULL | Serie del comprobante |
| `num_comprobante` | VARCHAR(20) | NO | - | Número de comprobante |
| `fecha_hora` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha y hora de venta |
| `impuesto` | DECIMAL(4,2) | NO | 0.00 | Porcentaje de IVA (ej: 5.00) |
| `total_venta` | DECIMAL(11,2) | NO | 0.00 | Total de la venta |
| `estado` | VARCHAR(1) | NO | 'P' | Estado: P=Pendiente, A=Aprobado, C=Cancelado |
| `metodo_pago` | VARCHAR(50) | YES | NULL | Método de pago |
| `tipo_distribucion` | VARCHAR(50) | YES | NULL | Tipo: Entrega, Envío |
| `fecha_entrega` | DATE | YES | NULL | Fecha estimada de entrega |
| `hora_entrega` | TIME | YES | NULL | Hora estimada de entrega |
| `direccion_envio` | VARCHAR(255) | YES | NULL | Dirección de envío |
| `comprobante_pago` | VARCHAR(255) | YES | NULL | Ruta de comprobante de pago |

**Índices:**
- PRIMARY KEY (`idventa`)
- INDEX (`estado`)
- INDEX (`fecha_hora`)
- FOREIGN KEY (`idcliente`) → `persona.idpersona`

**Relaciones:**
- ManyToOne con `persona` (cliente)
- OneToMany con `detalle_venta`

**Notas:**
- `estado` controla el flujo: P (Pendiente) → A (Aprobado) o C (Cancelado)
- Pedidos de tienda online inician con estado 'P' (requieren aprobación del admin)
- Ventas físicas inician con estado 'A' (aprobadas inmediatamente)
- `comprobante_pago` almacena ruta de imagen/PDF de comprobante subido por cliente

---

#### 4.2.6 Tabla: `detalle_venta`

**Propósito**: Detalle de artículos en cada venta

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `iddetalle_venta` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `idventa` | INT UNSIGNED | NO | - | FK a tabla venta |
| `idarticulo` | INT UNSIGNED | NO | - | FK a tabla articulo |
| `cantidad` | INT | NO | - | Cantidad vendida |
| `precio_venta` | DECIMAL(11,2) | NO | - | Precio unitario de venta |
| `descuento` | DECIMAL(11,2) | NO | 0.00 | Descuento aplicado |

**Índices:**
- PRIMARY KEY (`iddetalle_venta`)
- FOREIGN KEY (`idventa`) → `venta.idventa` (ON DELETE CASCADE)
- FOREIGN KEY (`idarticulo`) → `articulo.idarticulo`

**Relaciones:**
- ManyToOne con `venta`
- ManyToOne con `articulo`

**Notas:**
- ON DELETE CASCADE: si se elimina una venta, se eliminan todos sus detalles
- El precio se almacena en el detalle (no en articulo) para mantener historial

---

#### 4.2.7 Tabla: `ingreso`

**Propósito**: Registro de compras a proveedores

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `idingreso` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `idproveedor` | INT UNSIGNED | NO | - | FK a tabla persona |
| `tipo_comprobante` | VARCHAR(20) | NO | - | Tipo de comprobante |
| `serie_comprobante` | VARCHAR(20) | YES | NULL | Serie del comprobante |
| `num_comprobante` | VARCHAR(20) | NO | - | Número de comprobante |
| `fecha_hora` | DATETIME | NO | CURRENT_TIMESTAMP | Fecha y hora |
| `impuesto` | DECIMAL(4,2) | NO | 0.00 | Porcentaje de IVA |
| `total_compra` | DECIMAL(11,2) | NO | 0.00 | Total de la compra |
| `estado` | VARCHAR(1) | NO | 'A' | Estado: A=Aceptado, C=Cancelado |

**Índices:**
- PRIMARY KEY (`idingreso`)
- INDEX (`estado`)
- FOREIGN KEY (`idproveedor`) → `persona.idpersona`

**Relaciones:**
- ManyToOne con `persona` (proveedor)
- OneToMany con `detalle_ingreso`

---

#### 4.2.8 Tabla: `detalle_ingreso`

**Propósito**: Detalle de artículos en cada compra

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `iddetalle_ingreso` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `idingreso` | INT UNSIGNED | NO | - | FK a tabla ingreso |
| `idarticulo` | INT UNSIGNED | NO | - | FK a tabla articulo |
| `cantidad` | INT | NO | - | Cantidad comprada |
| `precio_compra` | DECIMAL(11,2) | NO | - | Precio unitario de compra |
| `precio_venta` | DECIMAL(11,2) | NO | - | Precio de venta sugerido |

**Índices:**
- PRIMARY KEY (`iddetalle_ingreso`)
- FOREIGN KEY (`idingreso`) → `ingreso.idingreso` (ON DELETE CASCADE)
- FOREIGN KEY (`idarticulo`) → `articulo.idarticulo`

**Relaciones:**
- ManyToOne con `ingreso`
- ManyToOne con `articulo`

**Notas:**
- `precio_venta` es el precio sugerido para la venta
- Al crear un ingreso, se actualiza el stock del artículo
- El precio de venta actual se obtiene del último detalle_ingreso del artículo

---

#### 4.2.9 Tabla: `roles`

**Propósito**: Definición de roles de usuario

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `idrol` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `nombre` | VARCHAR(50) | NO | - | Nombre del rol |
| `es_admin` | TINYINT(1) | NO | 0 | 1=Admin, 0=No admin |
| `descripcion` | VARCHAR(255) | YES | NULL | Descripción del rol |

**Datos iniciales:**
```sql
INSERT INTO roles VALUES
(1, 'Administrador', 1, 'Acceso completo al sistema'),
(2, 'Cliente', 0, 'Acceso solo a tienda y perfil');
```

---

#### 4.2.10 Tabla: `fechas_entrega`

**Propósito**: Gestión de fechas disponibles para entrega de pedidos

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `id` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `fecha` | DATE | NO | - | Fecha de entrega |
| `descripcion` | VARCHAR(150) | YES | NULL | Descripción opcional |
| `activo` | TINYINT(1) | NO | 1 | 1=Activa, 0=Inactiva |

---

#### 4.2.11 Tabla: `caja`

**Propósito**: Control de caja y arqueos

| Campo | Tipo | Null | Default | Descripción |
|-------|------|------|---------|-------------|
| `idcaja` | INT UNSIGNED | NO | AUTO_INCREMENT | Clave primaria |
| `fecha_apertura` | DATE | NO | - | Fecha de apertura |
| `monto_inicial` | DECIMAL(11,2) | NO | 0.00 | Monto inicial |
| `monto_final` | DECIMAL(11,2) | YES | NULL | Monto final al cerrar |
| `estado` | VARCHAR(20) | NO | 'Abierta' | Estado: Abierta, Cerrada |
| `observaciones` | TEXT | YES | NULL | Observaciones |

---

### 4.3 Relaciones Eloquent (Modelos)

#### 4.3.1 Modelo User

```php
// app/User.php
public function persona()
{
    return $this->belongsTo('sisVentas\Persona', 'idpersona');
}
```

**Uso:**
```php
$user = User::with('persona')->find(1);
echo $user->persona->nombre; // Accede a datos de la persona vinculada
```

#### 4.3.2 Modelo Persona

```php
// app/Persona.php
public function user()
{
    return $this->hasOne('sisVentas\User', 'idpersona');
}

public function ventas()
{
    return $this->hasMany('sisVentas\Venta', 'idcliente');
}

public function ingresos()
{
    return $this->hasMany('sisVentas\Ingreso', 'idproveedor');
}
```

**Uso:**
```php
$persona = Persona::with(['ventas', 'ingresos'])->find(1);
echo $persona->ventas->count(); // Total de ventas como cliente
echo $persona->ingresos->count(); // Total de compras como proveedor
```

#### 4.3.3 Modelo Articulo

```php
// app/Articulo.php
public function categoria()
{
    return $this->belongsTo('sisVentas\Categoria', 'idcategoria');
}

public function detallesVenta()
{
    return $this->hasMany('sisVentas\DetalleVenta', 'idarticulo');
}

public function detallesIngreso()
{
    return $this->hasMany('sisVentas\DetalleIngreso', 'idarticulo');
}
```

**Uso:**
```php
$articulo = Articulo::with('categoria')->find(1);
echo $articulo->categoria->nombre; // Nombre de la categoría
```

#### 4.3.4 Modelo Venta

```php
// app/Venta.php
public function cliente()
{
    return $this->belongsTo('sisVentas\Persona', 'idcliente');
}

public function detalles()
{
    return $this->hasMany('sisVentas\DetalleVenta', 'idventa');
}
```

**Uso:**
```php
$venta = Venta::with(['cliente', 'detalles.articulo'])->find(1);
echo $venta->cliente->nombre; // Nombre del cliente
foreach ($venta->detalles as $detalle) {
    echo $detalle->articulo->nombre; // Nombre del artículo
}
```

---

### 4.4 Migraciones

Las migraciones definen la estructura de la base de datos y permiten versionarla.

**Migraciones principales:**

1. `2014_10_12_000000_create_users_table.php` - Tabla users
2. `2014_10_12_100000_create_password_resets_table.php` - Password resets
3. `2026_05_01_160014_add_role_to_users_table.php` - Columna idrol
4. `2026_06_24_080522_add_soft_delete_to_persona_and_users.php` - Soft delete
5. `2026_06_24_090000_add_idpersona_to_users.php` - FK idpersona
6. `2026_06_24_090500_add_apellido_to_persona.php` - Columna apellido

**Ejecutar migraciones:**
```bash
php artisan migrate           # Ejecuta migraciones pendientes
php artisan migrate:rollback  # Revierte última migración
php artisan migrate:refresh   # Revierte y re-ejecuta todas
```

---

## 5. Módulos del Sistema

### 5.1 Módulo de Autenticación

**Propósito**: Control de acceso y sesiones de usuario

**Componentes:**
- `Auth/LoginController.php` - Login
- `Auth/RegisterController.php` - Registro de nuevos usuarios
- `Auth/ForgotPasswordController.php` - Recuperación de contraseña
- `Middleware/AdminMiddleware.php` - Verificación de rol admin

**Flujo de Login:**
```
1. Usuario ingresa email y password en /login
2. LoginController valida credenciales
3. Si es válido, verifica deleted_at (soft delete)
4. Redirige según rol:
   - idrol=1 (Admin) → /home (dashboard)
   - idrol=2 (Cliente) → /tienda
```

**Middleware de Autorización:**
```php
// AdminMiddleware.php
public function handle($request, Closure $next)
{
    if (auth()->user()->idrol !== 1) {
        return redirect('tienda');
    }
    return $next($request);
}
```

---

### 5.2 Módulo de Inventario

**Propósito**: Gestión de artículos y categorías

**Rutas:**
- `GET /almacen/articulo` - Listar artículos
- `GET /almacen/articulo/create` - Formulario de creación
- `POST /almacen/articulo` - Guardar artículo
- `GET /almacen/articulo/{id}/edit` - Formulario de edición
- `PUT /almacen/articulo/{id}` - Actualizar artículo
- `DELETE /almacen/articulo/{id}` - Eliminar artículo

**Funcionalidades:**
- Crear, editar, eliminar artículos
- Subir imágenes de productos
- Control de stock (actualización automática)
- Búsqueda por nombre, código, categoría
- Filtrado por estado (Activo/Inactivo)
- Alertas de stock bajo (< 5 unidades)

**Control de Stock:**
```php
// Al vender (VentaController@store)
$articulo->decrement('stock', $cantidad);

// Al comprar (IngresoController@store)
$articulo->increment('stock', $cantidad);
```

---

### 5.3 Módulo de Ventas

**Propósito**: Registro y gestión de ventas

**Rutas:**
- `GET /ventas/venta` - Listar ventas
- `GET /ventas/venta/create` - Formulario de creación
- `POST /ventas/venta` - Guardar venta
- `GET /ventas/venta/{id}` - Ver detalle
- `GET /ventas/venta/cambiarEstado/{id}` - Cambiar estado (P→A)
- `DELETE /ventas/venta/{id}` - Anular venta

**Estados de Venta:**
- **P (Pendiente)**: Pedido online esperando aprobación
- **A (Aprobado)**: Venta confirmada y procesada
- **C (Cancelado)**: Venta anulada (stock devuelto)

**Flujo de Anulación:**
```php
// VentaController@destroy
if ($venta->estado == 'P') {
    // Devolver stock
    foreach ($venta->detalles as $detalle) {
        $articulo->increment('stock', $detalle->cantidad);
    }
    // Cambiar estado
    $venta->estado = 'C';
    $venta->save();
}
```

---

### 5.4 Módulo de Compras (Ingresos)

**Propósito**: Registro de compras a proveedores

**Rutas:**
- `GET /compras/ingreso` - Listar ingresos
- `GET /compras/ingreso/create` - Formulario de creación
- `POST /compras/ingreso` - Guardar ingreso
- `GET /compras/ingreso/{id}` - Ver detalle
- `DELETE /compras/ingreso/{id}` - Anular ingreso

**Funcionalidades:**
- Registrar compras con múltiples artículos
- Actualizar stock automáticamente
- Registrar precio de compra y venta
- Historial de compras por proveedor

---

### 5.5 Módulo de Clientes y Proveedores

**Propósito**: Gestión de personas (clientes y proveedores)

**Rutas Clientes:**
- `GET /ventas/cliente` - Listar clientes
- `GET /ventas/cliente/create` - Crear cliente
- `PUT /ventas/cliente/{id}` - Editar cliente
- `DELETE /ventas/cliente/{id}` - Desactivar cliente

**Rutas Proveedores:**
- `GET /compras/proveedor` - Listar proveedores
- `GET /compras/proveedor/create` - Crear proveedor
- `PUT /compras/proveedor/{id}` - Editar proveedor
- `DELETE /compras/proveedor/{id}` - Desactivar proveedor

**Nota**: Se usa la misma tabla `persona` con campo `tipo_persona` para diferenciar.

---

### 5.6 Módulo de Tienda Online (E-commerce)

**Propósito**: Venta online de productos

**Rutas Públicas:**
- `GET /tienda` - Catálogo de productos
- `GET /tienda/articulo/{id}` - Detalle de producto

**Rutas Protegidas (requieren login):**
- `GET /tienda/checkout` - Checkout
- `POST /tienda/pedido` - Confirmar pedido
- `GET /tienda/perfil` - Perfil de usuario
- `GET /tienda/mis-compras` - Historial de pedidos

**Carrito de Compras:**
- Implementado con `localStorage` (clave: `andyland_cart_v2`)
- Persiste entre sesiones del navegador
- No requiere login para agregar productos
- Login requerido solo al hacer checkout

**Flujo de Pedido Online:**
```
1. Cliente agrega productos al carrito (localStorage)
2. Cliente va a /tienda/checkout
3. Si no está logueado → redirige a /login
4. Cliente confirma datos de envío y pago
5. Controller crea venta con estado='P' (pendiente)
6. Admin aprueba pedido en panel → estado='A'
7. Cliente puede ver estado en /tienda/mis-compras
```

**Verificación de Precio:**
```php
// TiendaController@store
// NO confiar en precio enviado por cliente
$precioReal = DB::table('detalle_ingreso')
    ->where('idarticulo', $idarticulo)
    ->orderBy('iddetalle_ingreso', 'desc')
    ->value('precio_venta');
```

---

### 5.7 Módulo de Perfil de Usuario

**Propósito**: Gestión de datos personales del cliente

**Rutas:**
- `GET /tienda/perfil` - Ver perfil
- `POST /tienda/perfil` - Actualizar datos
- `POST /tienda/perfil/password` - Cambiar contraseña
- `POST /tienda/perfil/email` - Cambiar email
- `GET /tienda/perfil/exportar` - Exportar datos (GDPR)
- `DELETE /tienda/perfil` - Desactivar cuenta (soft delete)

**Funcionalidades:**
- Editar datos personales (nombre, apellido, teléfono)
- Editar dirección de entrega
- Cambiar contraseña (requiere contraseña actual)
- Cambiar email (validación unique)
- Exportar datos personales en JSON (GDPR)
- Desactivar cuenta (soft delete, reversible 30 días)
- Ver categoría de cliente (calculada automáticamente)

**Categoría de Cliente (calculada):**
```php
// PerfilController@calcularCategoria
if ($totalPedidos === 0) {
    return 'Cliente Nueva';
} elseif ($totalGastado >= 500000 || $totalPedidos >= 10) {
    return 'Cliente VIP';
} elseif ($totalPedidos >= 4) {
    return 'Cliente Frecuente';
} else {
    return 'Cliente Regular';
}
```

**Exportación de Datos (GDPR):**
```php
// PerfilController@exportarDatos
$datos = [
    'usuario' => [...],
    'datos_personales' => [...],
    'categoria_cliente' => [...],
    'estadisticas' => [...],
    'historial_pedidos' => [...]
];
return response()->json($datos)->header('Content-Disposition', 'attachment');
```

**Soft Delete:**
```php
// PerfilController@eliminarCuenta
DB::table('persona')->where('idpersona', $id)->update(['deleted_at' => now()]);
DB::table('users')->where('id', $iduser)->update(['deleted_at' => now()]);
```

---

### 5.8 Módulo de Dashboard

**Propósito**: Panel de control con métricas en tiempo real

**Ruta:** `GET /home` (solo admin)

**Métricas mostradas:**
- Total de pedidos pendientes
- Total de artículos activos
- Artículos con stock bajo (< 5)
- Ventas del día
- Ingresos del mes

**API de Dashboard:**
- `GET /api/dashboard/stats` - Estadísticas generales
- `GET /api/dashboard/productos-mas-vendidos` - Top productos
- `GET /api/dashboard/ventas-por-mes` - Ventas mensuales (gráfico)

---

### 5.9 Módulo de Reportes

**Propósito**: Reportes detallados de ventas

**Ruta:** `GET /ventas/reporte`

**Funcionalidades:**
- Filtro por rango de fechas
- Filtro por estado (P, A, C)
- Total de ventas, cantidad, promedio
- Desglose por método de pago
- Gráfico de ventas por día
- Detalle de cada venta con artículos

---

### 5.10 Módulo de Caja

**Propósito**: Control de caja y arqueos

**Rutas:**
- `GET /admin/caja` - Ver caja actual
- `POST /admin/caja/abrir` - Abrir caja
- `POST /admin/caja/cerrar/{id}` - Cerrar caja
- `POST /admin/caja/movimiento` - Agregar movimiento
- `DELETE /admin/caja/movimiento/{id}` - Eliminar movimiento
- `GET /admin/caja/imprimir/{id}` - Imprimir arqueo

---

### 5.11 Módulo de Fechas de Entrega

**Propósito**: Gestión de fechas disponibles para entrega

**Rutas:**
- `GET /admin/fechas-entrega` - Listar fechas
- `POST /admin/fechas-entrega` - Crear fecha
- `PUT /admin/fechas-entrega/{id}` - Editar fecha
- `DELETE /admin/fechas-entrega/{id}` - Eliminar fecha
- `POST /admin/fechas-entrega/{id}/toggle` - Activar/desactivar
- `GET /admin/fechas-entrega/informe` - Informe de pedidos por fecha

---

## 6. API REST

### 6.1 Endpoints Disponibles

**Base URL:** `/api`

**Autenticación:** No requiere (uso interno)

#### 6.1.1 Artículos

```http
GET /api/articulos
```

**Respuesta:**
```json
[
  {
    "idarticulo": 1,
    "codigo": "ART-0001",
    "nombre": "Producto 1",
    "stock": 50,
    "precio": 150000,
    "imagen": "imagenes/articulos/producto1.jpg",
    "categoria": "Electrónica"
  }
]
```

```http
GET /api/articulos/{id}
```

```http
GET /api/articulos/stock-bajo
```

**Respuesta:** Lista de artículos con stock < 5

#### 6.1.2 Categorías

```http
GET /api/categorias
```

**Respuesta:**
```json
[
  {
    "idcategoria": 1,
    "nombre": "Electrónica",
    "descripcion": "Productos electrónicos"
  }
]
```

#### 6.1.3 Ventas

```http
GET /api/ventas
```

```http
GET /api/ventas/{id}
```

#### 6.1.4 Dashboard

```http
GET /api/dashboard/stats
```

**Respuesta:**
```json
{
  "total_pedidos": 150,
  "pedidos_pendientes": 5,
  "total_articulos": 120,
  "stock_bajo": 8,
  "ventas_hoy": 12,
  "ingresos_mes": 5000000
}
```

```http
GET /api/dashboard/productos-mas-vendidos
```

```http
GET /api/dashboard/ventas-por-mes
```

### 6.2 Consideraciones de Seguridad

**Advertencia:** Los endpoints API actualmente **no requieren autenticación**.

**Recomendaciones para producción:**
1. Agregar middleware `auth:api` a rutas sensibles
2. Implementar rate limiting
3. Validar origen de peticiones (CORS)
4. No exponer datos sensibles (passwords, emails completos)

**Ejemplo de protección:**
```php
// routes/api.php
Route::group(['middleware' => 'auth:api'], function () {
    Route::get('/ventas', 'Api\VentaController@index');
});
```

---

## 7. Seguridad y Autenticación

### 7.1 Autenticación

**Sistema:** Laravel Auth (session-based)

**Flujo:**
1. Usuario envía credenciales (email + password)
2. `Auth::attempt()` valida contra hash bcrypt
3. Si es válido, crea sesión en servidor
4. Cookie de sesión enviada al cliente
5. Subsecuentes requests incluyen cookie

**Configuración:**
```php
// config/auth.php
'driver' => 'session',
'model' => sisVentas\User::class,
```

### 7.2 Encriptación de Contraseñas

**Algoritmo:** bcrypt (cost 10)

```php
// Crear usuario
$user->password = Hash::make('password123');

// Validar contraseña
if (Hash::check('password123', $user->password)) {
    // Contraseña correcta
}
```

### 7.3 Protección CSRF

**Middleware:** `VerifyCsrfToken`

**Implementación:**
```blade
<form method="POST" action="/tienda/pedido">
    {{ csrf_field() }}
    <!-- o -->
    @csrf
</form>
```

**Excepciones:**
```php
// app/Http/Middleware/VerifyCsrfToken.php
protected $except = [
    // Rutas excluidas (si las hay)
];
```

### 7.4 Control de Acceso

**Middleware:** `isAdmin`

```php
// app/Http/Middleware/AdminMiddleware.php
public function handle($request, Closure $next)
{
    if (auth()->user()->idrol !== 1) {
        return redirect('tienda');
    }
    return $next($request);
}
```

**Uso en rutas:**
```php
Route::group(['middleware' => ['auth', 'isAdmin']], function () {
    // Rutas solo para administradores
    Route::get('/home', 'HomeController@index');
});
```

### 7.5 Soft Delete (Cuentas Desactivadas)

**Propósito:** Permitir desactivar cuentas sin eliminar datos históricos

**Implementación:**
```php
// Desactivar cuenta
DB::table('users')->where('id', $id)->update(['deleted_at' => now()]);

// LoginController: excluir cuentas desactivadas
protected function credentials(Request $request)
{
    return array_merge($request->only($this->username(), 'password'), [
        'deleted_at' => null,
    ]);
}
```

### 7.6 Validación de Datos

**Form Requests:**
```php
// app/Http/Requests/VentaFormRequest.php
public function rules()
{
    return [
        'idcliente' => 'required|exists:persona,idpersona',
        'total_venta' => 'required|numeric|min:0',
    ];
}
```

**Validación en Controller:**
```php
$validator = Validator::make($request->all(), [
    'email' => 'required|email|unique:users',
    'password' => 'required|min:6|confirmed',
]);
```

### 7.7 Protección contra Inyección SQL

**Eloquent ORM:** Usa prepared statements automáticamente

```php
// Seguro (Eloquent)
$user = User::where('email', $email)->first();

// Peligroso (raw query sin sanitizar)
$user = DB::select("SELECT * FROM users WHERE email = '$email'");
```

### 7.8 Logout Seguro

**Implementación:**
```php
// routes/web.php
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// Vista
<form action="{{ route('logout') }}" method="POST">
    {{ csrf_field() }}
    <button type="submit">Cerrar Sesión</button>
</form>
```

**Nota:** Se usa POST en vez de GET para prevenir CSRF logout attacks.

---

## 8. Instalación y Despliegue

### 8.1 Requisitos del Sistema

**Desarrollo:**
- PHP >= 7.4
- Composer >= 2.0
- Node.js >= 16.x
- npm >= 8.x
- MySQL >= 5.7
- Docker >= 20.10 (opcional, recomendado)
- Docker Compose >= 3.8 (opcional, recomendado)

**Producción:**
- Servidor web: Apache 2.4+ o Nginx 1.18+
- PHP >= 7.4 con extensiones requeridas
- MySQL >= 5.7
- SSL/TLS certificado (Let's Encrypt recomendado)
- 2GB RAM mínimo
- 20GB disco mínimo

### 8.2 Instalación Local con Docker (Recomendado)

#### Paso 1: Clonar Repositorio

```bash
git clone git@github.com:rootpy-90/andyland-sisventas.git
cd andyland-sisventas
```

#### Paso 2: Configurar Variables de Entorno

```bash
cp .env.example .env
```

Editar `.env`:
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

#### Paso 3: Levantar Contenedores

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

#### Paso 4: Generar APP_KEY

```bash
docker-compose exec app php artisan key:generate
```

#### Paso 5: Ejecutar Migraciones

```bash
docker-compose exec app php artisan migrate --force
```

#### Paso 6: Poblar Base de Datos (Opcional)

```bash
docker-compose exec app php artisan db:seed --force
```

**Usuarios creados:**
- Admin: `admin@andyland.com` / `admin123`
- Cliente: `cliente@andyland.com` / `cliente123`

#### Paso 7: Acceder a la Aplicación

- **Aplicación:** http://localhost:8000
- **phpMyAdmin:** http://localhost:8081 (perfil: tools)
  ```bash
  docker-compose --profile tools up -d
  ```

#### Comandos Útiles

```bash
# Ver logs en tiempo real
docker-compose logs -f app

# Entrar al contenedor
docker-compose exec app bash

# Dentro del contenedor:
php artisan migrate          # Migraciones
php artisan db:seed          # Seeders
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

### 8.3 Instalación Local sin Docker

#### Paso 1: Instalar Dependencias

```bash
# PHP dependencies
composer install

# Node dependencies
npm install
```

#### Paso 2: Configurar .env

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

#### Paso 3: Generar APP_KEY

```bash
php artisan key:generate
```

#### Paso 4: Crear Base de Datos

```bash
mysql -u root -p -e "CREATE DATABASE dbventaslaravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

#### Paso 5: Ejecutar Migraciones

```bash
php artisan migrate
```

#### Paso 6: Poblar Datos (Opcional)

```bash
php artisan db:seed
```

#### Paso 7: Compilar Assets

```bash
npm run dev
```

#### Paso 8: Iniciar Servidor

```bash
php artisan serve --port=8000
```

Acceder a: http://localhost:8000

---

### 8.4 Despliegue en Producción

#### 8.4.1 Preparación del Código

```bash
# 1. Clonar repositorio en servidor
git clone git@github.com:rootpy-90/andyland-sisventas.git /var/www/andyland
cd /var/www/andyland

# 2. Instalar dependencias de producción
composer install --optimize-autoloader --no-dev

# 3. Configurar .env para producción
cp .env.example .env
nano .env
```

**Configuración .env producción:**
```env
APP_NAME="Andyland PY - SisVentas"
APP_ENV=production
APP_KEY=base64:... (generar con php artisan key:generate)
APP_DEBUG=false
APP_URL=https://andyland.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dbventaslaravel
DB_USERNAME=andyland_user
DB_PASSWORD=strong_password_here

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_DRIVER=sync
```

#### 8.4.2 Permisos de Directorios

```bash
# Propietario
sudo chown -R www-data:www-data /var/www/andyland

# Permisos
sudo chmod -R 755 /var/www/andyland
sudo chmod -R 775 /var/www/andyland/storage
sudo chmod -R 775 /var/www/andyland/bootstrap/cache
```

#### 8.4.3 Configurar Apache

**Crear virtual host:**
```bash
sudo nano /etc/apache2/sites-available/andyland.conf
```

**Contenido:**
```apache
<VirtualHost *:80>
    ServerName andyland.com
    ServerAlias www.andyland.com
    DocumentRoot /var/www/andyland/public

    <Directory /var/www/andyland/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/andyland_error.log
    CustomLog ${APACHE_LOG_DIR}/andyland_access.log combined
</VirtualHost>
```

**Habilitar sitio:**
```bash
sudo a2ensite andyland.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

#### 8.4.4 Configurar SSL (Let's Encrypt)

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache

# Obtener certificado
sudo certbot --apache -d andyland.com -d www.andyland.com

# Renovación automática (ya configurada por Certbot)
sudo certbot renew --dry-run
```

#### 8.4.5 Optimizar para Producción

```bash
# Limpiar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Generar autoload optimizado
composer dump-autoload --optimize --classmap-authoritative
```

#### 8.4.6 Configurar Backup de Base de Datos

**Script de backup diario:**
```bash
sudo nano /usr/local/bin/backup-andyland.sh
```

**Contenido:**
```bash
#!/bin/bash
BACKUP_DIR="/backups/andyland"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="dbventaslaravel"
DB_USER="andyland_user"
DB_PASS="strong_password_here"

mkdir -p $BACKUP_DIR

mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/backup_$DATE.sql.gz

# Eliminar backups antiguos (> 30 días)
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +30 -delete
```

**Permisos y cron:**
```bash
sudo chmod +x /usr/local/bin/backup-andyland.sh

# Agregar a cron (diario a las 2 AM)
sudo crontab -e
# Agregar línea:
0 2 * * * /usr/local/bin/backup-andyland.sh
```

#### 8.4.7 Checklist de Producción

- [ ] `APP_DEBUG=false` en .env
- [ ] `APP_ENV=production` en .env
- [ ] SSL/TLS configurado (HTTPS)
- [ ] Permisos correctos en storage/ y bootstrap/cache/
- [ ] Contraseña fuerte en BD
- [ ] Backup automático configurado
- [ ] Caché de configuración generado
- [ ] Caché de rutas generado
- [ ] Logs monitoreados
- [ ] Firewall configurado (solo puertos 80, 443, 22)
- [ ] Fail2Ban instalado (protección contra brute force)
- [ ] Actualizaciones de seguridad programadas

---

### 8.5 Troubleshooting

#### Error: "Connection refused" en BD

**Causa:** MySQL no está corriendo o credenciales incorrectas

**Solución:**
```bash
# Verificar que MySQL esté corriendo
docker-compose ps db

# Ver logs de MySQL
docker-compose logs db

# Verificar credenciales en .env
grep DB_ .env
```

#### Error: "Class not found" después de composer install

**Causa:** Autoload desactualizado

**Solución:**
```bash
composer dump-autoload
```

#### Error: "Permission denied" en storage/

**Causa:** Permisos incorrectos

**Solución:**
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### Error: "SQLSTATE[42S22]: Column not found"

**Causa:** Migraciones pendientes

**Solución:**
```bash
php artisan migrate
```

#### Assets no cargan (404 en CSS/JS)

**Causa:** Assets no compilados

**Solución:**
```bash
npm run production
```

---

## 9. Anexos

### 9.1 Glosario de Términos

- **Artículo**: Producto disponible para venta
- **Categoría**: Clasificación de artículos
- **Cliente**: Persona que compra productos
- **Proveedor**: Persona/empresa que suministra mercadería
- **Venta**: Registro de transacción de venta
- **Ingreso**: Registro de compra a proveedor
- **Comprobante**: Documento que acredita la transacción (Ticket, Factura, Boleta)
- **Stock**: Cantidad disponible de un artículo
- **Soft Delete**: Eliminación lógica (no física) de registros
- **E-commerce**: Comercio electrónico (tienda online)
- **GDPR**: Reglamento General de Protección de Datos (UE)

### 9.2 Créditos

**Desarrollado por:** [Nombre del estudiante]

**Universidad:** [Nombre de la universidad]

**Carrera:** [Nombre de la carrera]

**Año:** 2026

**Tutor:** [Nombre del tutor]

### 9.3 Licencia

Este proyecto fue desarrollado con fines académicos como proyecto de tesis.

---

**Fin del Documento**

*Última actualización: 24 de Junio de 2026*

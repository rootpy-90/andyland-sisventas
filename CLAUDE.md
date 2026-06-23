# CLAUDE.md — Contexto y memoria del proyecto sisVentas

## Información general

- **Proyecto:** Sistema de Ventas con E-commerce — "Andyland PY"
- **Framework:** Laravel 5.x (PHP 7.4)
- **BD:** MySQL en `127.0.0.1:3307`, base de datos `dbventaslaravel`, usuario `root` sin contraseña
- **Servidor dev:** `php artisan serve --port=8000` → `http://127.0.0.1:8000`
- **Directorio raíz:** `c:\sisVentas`
- **Namespace PHP:** `sisVentas` (no `App`)

---

## Estructura del proyecto

```
c:\sisVentas
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/                    ← Controladores JSON API (creados Jun 2026)
│   │   │   │   ├── ArticuloController.php
│   │   │   │   ├── CategoriaController.php
│   │   │   │   ├── VentaController.php
│   │   │   │   └── DashboardController.php
│   │   │   ├── Auth/
│   │   │   ├── ArticuloController.php
│   │   │   ├── CategoriaController.php
│   │   │   ├── ClienteController.php
│   │   │   ├── HomeController.php
│   │   │   ├── IngresoController.php
│   │   │   ├── ProveedorController.php
│   │   │   ├── RegistroController.php
│   │   │   ├── TiendaController.php    ← Controlador e-commerce principal
│   │   │   ├── UsuarioController.php
│   │   │   └── VentaController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php     ← isAdmin: redirige a /tienda si idrol != 1
│   ├── Articulo.php     (tabla: articulo,     PK: idarticulo)
│   ├── Categoria.php    (tabla: categoria,    PK: idcategoria)
│   ├── DetalleIngreso.php
│   ├── DetalleVenta.php
│   ├── Ingreso.php
│   ├── Persona.php      (tabla: persona,      PK: idpersona)
│   ├── Registro.php
│   ├── User.php         (tabla: users, campos: id, name, email, password, idrol, idpersona)
│   └── Venta.php
├── resources/views/
│   ├── layouts/
│   │   ├── admin.blade.php    ← Layout AdminLTE skin-red (panel admin)
│   │   ├── tienda.blade.php   ← Layout e-commerce con carrito drawer (creado Jun 2026)
│   │   └── app.blade.php
│   ├── tienda/
│   │   ├── index.blade.php    ← Catálogo público con grid de productos
│   │   ├── show.blade.php     ← Detalle de producto
│   │   ├── checkout.blade.php ← Confirmación de pedido (requiere auth)
│   │   └── search.blade.php
│   ├── ventas/venta/
│   │   ├── index.blade.php    ← Tabla de ventas con badges de estado
│   │   ├── show.blade.php
│   │   ├── comprobante.blade.php
│   │   ├── create.blade.php
│   │   └── modal.blade.php
│   ├── home.blade.php         ← Dashboard con 3 cards (pedidos, artículos, stock)
│   └── ...resto de vistas admin
├── routes/
│   ├── web.php    ← Ver sección de rutas abajo
│   └── api.php    ← Endpoints JSON REST
└── public/
    └── imagenes/articulos/    ← Imágenes de productos subidas
```

---

## Roles de usuario

| idrol | Rol      | Acceso                                        |
|-------|----------|-----------------------------------------------|
| 1     | Admin    | Panel completo (`/home`, `/almacen`, etc.)    |
| 2     | Cliente  | Solo tienda (`/tienda`) y checkout            |

- `AdminMiddleware` bloquea cualquier ruta admin si `idrol != 1` y redirige a `/tienda`
- Después del login, `HomeController` redirige clientes (idrol==2) a `/tienda`

---

## Rutas (web.php)

```
GET  /                    → redirect /tienda             (público)
GET  /tienda              → TiendaController@index       (público)
GET  /tienda/articulo/{id}→ TiendaController@show        (público)
GET  /tienda/checkout     → TiendaController@checkout    (auth)
POST /tienda/pedido       → TiendaController@store       (auth)
POST /completar-perfil    → TiendaController@completarPerfil (auth)

GET  /home                → HomeController@index         (auth + isAdmin)
CRUD /almacen/categoria   → CategoriaController          (auth + isAdmin)
CRUD /almacen/articulo    → ArticuloController           (auth + isAdmin)
CRUD /ventas/cliente      → ClienteController            (auth + isAdmin)
CRUD /compras/proveedor   → ProveedorController          (auth + isAdmin)
CRUD /compras/ingreso     → IngresoController            (auth + isAdmin)
CRUD /ventas/venta        → VentaController              (auth + isAdmin)
CRUD /seguridad/usuario   → UsuarioController            (auth + isAdmin)
CRUD /registros/registro  → RegistroController           (auth + isAdmin)
GET  /ventas/venta/cambiarEstado/{id}                    (auth + isAdmin)
GET  /ventas/venta/comprobante/{id}                      (auth + isAdmin)
```

## Rutas API (api.php) — sin autenticación

```
GET  /api/articulos
GET  /api/articulos/stock-bajo
GET  /api/articulos/{id}
GET  /api/categorias
GET  /api/ventas
GET  /api/ventas/{id}
GET  /api/dashboard/stats
GET  /api/dashboard/productos-mas-vendidos
GET  /api/dashboard/ventas-por-mes
```

---

## Esquema de base de datos (tablas principales)

| Tabla           | PK                | Campos clave                                                          |
|-----------------|-------------------|-----------------------------------------------------------------------|
| users           | id                | name, email, password, idrol, idpersona                               |
| persona         | idpersona         | tipo_persona, nombre, tipo_documento, num_documento, direccion, telefono, email |
| articulo        | idarticulo        | idcategoria, codigo, nombre, stock, descripcion, tiempo_entrega, imagen, estado |
| categoria       | idcategoria       | nombre, condicion                                                     |
| venta           | idventa           | idcliente, tipo_comprobante, serie_comprobante, num_comprobante, total_venta, fecha_hora, impuesto, estado |
| detalle_venta   | iddetalle_venta   | idventa, idarticulo, cantidad, descuento, precio_venta                |
| detalle_ingreso | iddetalle_ingreso | idarticulo, precio_venta, ...                                         |

**Estado de venta:** `P` = Pendiente, `A` = Aprobado/Aceptado, `C` = Cancelado

**Precio de artículos:** Se obtiene de `detalle_ingreso` (último registro por `idarticulo`). La tabla `articulo` no tiene columna `precio`.

---

## Flujo del carrito e-commerce

1. **Navegación pública** — cualquier visitante puede ver el catálogo y detalle de productos
2. **Carrito** — funciona con `localStorage` (clave: `andyland_cart_v2`), no requiere login
3. **Checkout** — ruta protegida: si el visitante no está logueado, Laravel lo redirige al login y luego de vuelta al checkout
4. **Confirmar pedido** — el front envía el carrito como JSON (`cart_json`); el backend **verifica el precio real en BD** (no confía en el precio del cliente)
5. **Post-orden** — flash session `order_placed: true` dispara `Cart.clear()` en el layout JS

---

## Diseño / UI

- **Panel admin:** AdminLTE 2.4.18, tema `skin-red` (rojo), sidebar oscuro (`#1a1a2e`)
- **Tienda cliente:** Layout propio (`layouts/tienda.blade.php`), paleta roja/blanca, sin AdminLTE
- **Cart drawer:** Panel deslizante desde la derecha, sticky en todas las páginas de tienda
- **Dashboard:** 3 cards con degradado (rojo, oscuro, naranja) + hover elevado
- **Tabla ventas:** Header oscuro, badges de estado suaves (verde/amarillo/rojo)

---

## Comandos útiles

```bash
# Levantar servidor
php artisan serve --port=8000

# Limpiar caché
php artisan view:clear
php artisan route:clear
php artisan cache:clear

# Ver rutas
php artisan route:list
php artisan route:list --path=api
```

---

## Notas y decisiones importantes

- **Namespace:** El proyecto usa `sisVentas\` (no `App\`) en todos los modelos y controladores
- **Timestamps:** Los modelos tienen `public $timestamps = false` — las tablas no tienen `created_at`/`updated_at`
- **Imágenes:** Se guardan en `public/imagenes/articulos/` con el nombre original del archivo
- **Precio:** Nunca confiar en el precio enviado por el cliente; siempre verificar en `detalle_ingreso`
- **Login redirect:** Clientes (idrol=2) → `/tienda`; Admins (idrol=1) → `/home`
- **API:** Sin autenticación (sistema interno local); proteger con `auth:api` si se expone externamente
- **Puerto BD:** MySQL en puerto **3307** (no el 3306 estándar)

---

## Directorio `laravel/` en la raíz

Existe un directorio `c:\sisVentas\laravel\` que parece ser un esqueleto vacío duplicado del proyecto (sin `vendor/` ni `node_modules/`). **Pendiente de confirmar con el usuario si se puede eliminar.**

---

## Historial de cambios relevantes (sesiones Claude)

### Mayo 2026
- Sistema base funcionando con roles admin/cliente
- Vistas de tienda, catálogo, venta, comprobante
- Dashboard con cards de métricas
- Campo `tiempo_entrega` en artículos
- Flujo de pedido desde tienda con modal de datos de cliente
- Estado de venta: Pendiente → Aprobado con botón en admin
- Anulación de venta con devolución de stock

### Junio 2026 (sesión actual)
- Rediseño completo a tema **rojo** (skin-red AdminLTE)
- Cards modernas en dashboard con degradados y hover
- API REST JSON en `/api/*` (sin auth, para consumo interno/Chart.js)
- **E-commerce completo:**
  - Layout `layouts/tienda.blade.php` con navbar, buscador, cart drawer
  - Carrito con `localStorage`, edición de cantidades, total en tiempo real
  - Tienda pública (sin login), checkout protegido (con login)
  - Página de detalle de producto con selector de cantidad
  - Checkout con tabla de pedido y datos del comprador
  - Backend verifica precios en BD al confirmar pedido
  - Pedido se crea con estado `P` (pendiente) para aprobación del admin

-- ============================================
-- Script de inicialización de BD para sisVentas
-- Se ejecuta automáticamente al crear el contenedor
-- ============================================

-- Crear tabla roles (FALTANTE en migraciones)
CREATE TABLE IF NOT EXISTS `roles` (
  `idrol` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `es_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `descripcion` VARCHAR(255) NULL,
  PRIMARY KEY (`idrol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar roles básicos
INSERT INTO `roles` (`idrol`, `nombre`, `es_admin`, `descripcion`) VALUES
(1, 'Administrador', 1, 'Acceso completo al sistema'),
(2, 'Cliente', 0, 'Acceso solo a tienda y perfil')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Crear tabla persona (si no existe)
CREATE TABLE IF NOT EXISTS `persona` (
  `idpersona` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tipo_persona` VARCHAR(20) NOT NULL DEFAULT 'Cliente',
  `nombre` VARCHAR(100) NOT NULL,
  `apellido` VARCHAR(100) NULL,
  `tipo_documento` VARCHAR(20) NULL,
  `num_documento` VARCHAR(20) NULL,
  `direccion` VARCHAR(255) NULL,
  `telefono` VARCHAR(20) NULL,
  `email` VARCHAR(100) NULL,
  `ciudad` VARCHAR(100) NULL,
  `barrio` VARCHAR(100) NULL,
  `pais` VARCHAR(50) NULL,
  `referencia` TEXT NULL,
  `categoria` VARCHAR(50) NULL DEFAULT 'Nueva',
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`idpersona`),
  INDEX `idx_tipo_persona` (`tipo_persona`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  `idrol` INT NOT NULL DEFAULT 2,
  `idpersona` INT UNSIGNED NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  FOREIGN KEY (`idpersona`) REFERENCES `persona`(`idpersona`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla categoria
CREATE TABLE IF NOT EXISTS `categoria` (
  `idcategoria` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NULL,
  `condicion` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idcategoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla articulo
CREATE TABLE IF NOT EXISTS `articulo` (
  `idarticulo` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idcategoria` INT UNSIGNED NOT NULL,
  `codigo` VARCHAR(50) NULL,
  `nombre` VARCHAR(200) NOT NULL,
  `stock` INT NOT NULL DEFAULT 0,
  `descripcion` TEXT NULL,
  `imagen` VARCHAR(255) NULL,
  `estado` VARCHAR(20) NOT NULL DEFAULT 'Activo',
  `tiempo_entrega` INT NULL DEFAULT 0,
  PRIMARY KEY (`idarticulo`),
  FOREIGN KEY (`idcategoria`) REFERENCES `categoria`(`idcategoria`) ON DELETE RESTRICT,
  INDEX `idx_estado` (`estado`),
  INDEX `idx_stock` (`stock`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla venta
CREATE TABLE IF NOT EXISTS `venta` (
  `idventa` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idcliente` INT UNSIGNED NOT NULL,
  `tipo_comprobante` VARCHAR(20) NOT NULL,
  `serie_comprobante` VARCHAR(20) NULL,
  `num_comprobante` VARCHAR(20) NOT NULL,
  `fecha_hora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `impuesto` DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  `total_venta` DECIMAL(11,2) NOT NULL DEFAULT 0.00,
  `estado` VARCHAR(1) NOT NULL DEFAULT 'P',
  PRIMARY KEY (`idventa`),
  FOREIGN KEY (`idcliente`) REFERENCES `persona`(`idpersona`) ON DELETE RESTRICT,
  INDEX `idx_estado` (`estado`),
  INDEX `idx_fecha` (`fecha_hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla detalle_venta
CREATE TABLE IF NOT EXISTS `detalle_venta` (
  `iddetalle_venta` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idventa` INT UNSIGNED NOT NULL,
  `idarticulo` INT UNSIGNED NOT NULL,
  `cantidad` INT NOT NULL,
  `precio_venta` DECIMAL(11,2) NOT NULL,
  `descuento` DECIMAL(11,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`iddetalle_venta`),
  FOREIGN KEY (`idventa`) REFERENCES `venta`(`idventa`) ON DELETE CASCADE,
  FOREIGN KEY (`idarticulo`) REFERENCES `articulo`(`idarticulo`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla ingreso
CREATE TABLE IF NOT EXISTS `ingreso` (
  `idingreso` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idproveedor` INT UNSIGNED NOT NULL,
  `tipo_comprobante` VARCHAR(20) NOT NULL,
  `serie_comprobante` VARCHAR(20) NULL,
  `num_comprobante` VARCHAR(20) NOT NULL,
  `fecha_hora` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `impuesto` DECIMAL(4,2) NOT NULL DEFAULT 0.00,
  `total_compra` DECIMAL(11,2) NOT NULL DEFAULT 0.00,
  `estado` VARCHAR(1) NOT NULL DEFAULT 'A',
  PRIMARY KEY (`idingreso`),
  FOREIGN KEY (`idproveedor`) REFERENCES `persona`(`idpersona`) ON DELETE RESTRICT,
  INDEX `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla detalle_ingreso
CREATE TABLE IF NOT EXISTS `detalle_ingreso` (
  `iddetalle_ingreso` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `idingreso` INT UNSIGNED NOT NULL,
  `idarticulo` INT UNSIGNED NOT NULL,
  `cantidad` INT NOT NULL,
  `precio_compra` DECIMAL(11,2) NOT NULL,
  `precio_venta` DECIMAL(11,2) NOT NULL,
  PRIMARY KEY (`iddetalle_ingreso`),
  FOREIGN KEY (`idingreso`) REFERENCES `ingreso`(`idingreso`) ON DELETE CASCADE,
  FOREIGN KEY (`idarticulo`) REFERENCES `articulo`(`idarticulo`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla caja
CREATE TABLE IF NOT EXISTS `caja` (
  `idcaja` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha_apertura` DATE NOT NULL,
  `monto_inicial` DECIMAL(11,2) NOT NULL DEFAULT 0.00,
  `monto_final` DECIMAL(11,2) NULL,
  `estado` VARCHAR(20) NOT NULL DEFAULT 'Abierta',
  `observaciones` TEXT NULL,
  PRIMARY KEY (`idcaja`),
  INDEX `idx_fecha` (`fecha_apertura`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla fechas_entrega
CREATE TABLE IF NOT EXISTS `fechas_entrega` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `fecha` DATE NOT NULL,
  `descripcion` VARCHAR(255) NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_fecha` (`fecha`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

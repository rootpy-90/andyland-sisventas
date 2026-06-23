-- ============================================================
-- Módulo Caja — AndylandPy
-- Ejecutar en: dbventaslaravel (puerto 3307)
-- ============================================================

CREATE TABLE IF NOT EXISTS `caja` (
  `id`                   INT AUTO_INCREMENT PRIMARY KEY,
  `fecha_apertura`       DATE NOT NULL,
  `hora_apertura`        TIME NOT NULL,
  `monto_inicial`        DECIMAL(15,2) NOT NULL DEFAULT 0,
  `observacion`          TEXT,
  `estado`               ENUM('abierta','cerrada') DEFAULT 'abierta',
  `hora_cierre`          TIME DEFAULT NULL,
  `fecha_cierre`         DATETIME DEFAULT NULL,
  `monto_final`          DECIMAL(15,2) DEFAULT NULL,
  `observacion_cierre`   TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `arqueo_caja` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `caja_id`     INT NOT NULL,
  `tipo`        ENUM('ingreso','egreso') NOT NULL,
  `descripcion` VARCHAR(200) NOT NULL,
  `monto`       DECIMAL(15,2) NOT NULL,
  `metodo`      VARCHAR(50) DEFAULT 'Efectivo',
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_arqueo_caja` FOREIGN KEY (`caja_id`) REFERENCES `caja`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

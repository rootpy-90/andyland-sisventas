-- ============================================================
-- Actualización de nombres e imágenes de artículos - Andyland PY
-- ============================================================

USE dbventaslaravel;

-- Artículo basura (screenshot) → Inactivo
UPDATE articulo SET estado = 'Inactivo'
  WHERE idarticulo = 11;

-- ---- PORTA ANILLOS (ya existen en articulos, solo actualizar imagen) ----
-- Los artículos 5-10 (novios) ya tienen imagen correcta, solo copiar era necesario.

-- ---- RAMOS ----
UPDATE articulo SET nombre = 'Bouquet Romántico',  imagen = 'Bouquet Romántico.png'  WHERE idarticulo = 12;
UPDATE articulo SET nombre = 'Ramo Eterno',        imagen = 'Ramo Eterno.png'        WHERE idarticulo = 13;
UPDATE articulo SET nombre = 'Ramo Premium',       imagen = 'Ramo Premium.png'       WHERE idarticulo = 14;

-- ---- TAZAS ----
UPDATE articulo SET nombre = 'Taza Personalizada - Dentista', imagen = 'TAZA Personalizada- Dentista.png' WHERE idarticulo = 16;
UPDATE articulo SET nombre = 'Taza Personalizada - Doctora',  imagen = 'TAZA Personalizada- Doctora.png'  WHERE idarticulo = 17;
UPDATE articulo SET nombre = 'Taza Personalizada - Maestra',  imagen = 'TAZA Personalizada- Maestra.png'  WHERE idarticulo = 18;
UPDATE articulo SET nombre = 'Taza Personalizada - Oficial',  imagen = 'TAZA Personalizada- Oficial.png'  WHERE idarticulo = 19;

-- ---- TOCADOS (9 artículos, 9 imágenes) ----
UPDATE articulo SET nombre = 'Tocado con Flores y Perlas',      imagen = 'TOCADO CON FLORES Y PERLAS.png'       WHERE idarticulo = 20;
UPDATE articulo SET nombre = 'Tocado con Perlas bordado a mano',imagen = 'TOCADO CON PERLAS bordado a mano.png' WHERE idarticulo = 21;
UPDATE articulo SET nombre = 'Tocado Flores Celestes',          imagen = 'TOCADO FLORES CELESTES.png'           WHERE idarticulo = 22;
UPDATE articulo SET nombre = 'Tocado Novia Elegante',           imagen = 'TOCADO NOVIA Elegante.png'            WHERE idarticulo = 23;
UPDATE articulo SET nombre = 'Tocado para Bautismo',            imagen = 'TOCADO PARA BAUTISMO.png'             WHERE idarticulo = 24;
UPDATE articulo SET nombre = 'Tocado para Novia Sencilla',      imagen = 'TOCADO PARA NOVIA sencilla.png'       WHERE idarticulo = 25;
UPDATE articulo SET nombre = 'Peineta con Flores Blancas',      imagen = 'Peineta CON FLORES BLANCAS.png'       WHERE idarticulo = 26;
UPDATE articulo SET nombre = 'Peineta con Rosas',               imagen = 'PEINETA con rosas.png'                WHERE idarticulo = 27;
UPDATE articulo SET nombre = 'Vincha Elegante',                 imagen = 'Vincha ELEGANTE.png'                  WHERE idarticulo = 28;

-- ---- TOPPERCAKE ----
UPDATE articulo SET nombre = 'Topper - Egresada',         imagen = 'Egresada.png'              WHERE idarticulo = 32;
UPDATE articulo SET nombre = 'Topper Personalizado',      imagen = 'Pesonalizado.png'          WHERE idarticulo = 33;
UPDATE articulo SET nombre = 'Topper - Primer añito princesa', imagen = 'Primer añito princesa.png' WHERE idarticulo = 34;
UPDATE articulo SET nombre = 'Nena con mascota',          imagen = 'Nena con mascota.png'      WHERE idarticulo = 31;
UPDATE articulo SET nombre = 'Niño 2 años',               imagen = 'Niño 2 años.png'           WHERE idarticulo = 35;

-- ---- NOVIOS (TOPPERCAKE área) ----
UPDATE articulo SET nombre = 'Novios - Modelo Bombero y Abogada', imagen = 'NOVIOS-Modelo Bombero y Abogada.png' WHERE idarticulo = 36;

-- ---- SOUVENIR ----
UPDATE articulo SET nombre = 'Souvenir Sirena', imagen = 'Souvenir Sirena.png' WHERE idarticulo = 46;
UPDATE articulo SET nombre = 'Souvenir Bella',  imagen = 'Souvenir Bella.png'  WHERE idarticulo = 44;

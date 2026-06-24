# ============================================
# Stage 1: Dependencies (Composer)
# ============================================
FROM composer:2.2 AS dependencies

WORKDIR /app

# Copiar archivos de composer y carpetas requeridas para autoload
COPY composer.json composer.lock ./
COPY database/ ./database/

# Instalar dependencias de producción
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# ============================================
# Stage 2: Production Runtime
# ============================================
FROM php:7.4-apache AS production

# Instalar dependencias del sistema + curl para healthcheck
RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Habilitar módulos de Apache
RUN a2enmod rewrite headers

# Configurar Apache para Laravel
RUN echo '<VirtualHost *:80>\n\
    DocumentRoot /var/www/public\n\
    <Directory /var/www/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Configurar PHP para producción
RUN echo "memory_limit = 256M\n\
upload_max_filesize = 10M\n\
post_max_size = 10M\n\
max_execution_time = 60" > /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www

# Copiar dependencias de Composer desde stage anterior
COPY --from=dependencies --chown=www-data:www-data /app/vendor ./vendor

# Copiar código de la aplicación
COPY --chown=www-data:www-data . .

# Copiar .env.example para que artisan funcione
COPY --chown=www-data:www-data .env.example .env

# Copiar entrypoint script
COPY --chown=www-data:www-data docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Crear directorios necesarios
RUN mkdir -p /var/www/storage/app/public \
    /var/www/public/comprobantes \
    /var/www/public/imagenes/articulos \
    && chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Exponer puerto
EXPOSE 80

# Healthcheck
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:80 || exit 1

# Usar entrypoint script
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

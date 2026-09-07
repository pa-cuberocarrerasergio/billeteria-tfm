FROM php:8.2-cli

# Instalar dependencias del sistema y extensiones de PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    libpng-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install pdo pdo_pgsql bcmath gd zip

# Copiar Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar los archivos del proyecto backend
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-dev --optimize-autoloader

# Exponer el puerto
EXPOSE 8080

# Limpiar/optimizar caché, ejecutar migraciones, sembrar datos iniciales e iniciar el servidor en el puerto de Render
CMD php artisan config:cache && php artisan route:cache && php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}

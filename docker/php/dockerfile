# Usa una imagen de PHP con FPM incluido
FROM php:8.0-fpm

# Instala extensiones necesarias para MySQL y otras dependencias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Asegura que el usuario www-data tenga permisos
RUN chown -R www-data:www-data /var/www/html

# Expone el puerto 9000 para PHP-FPM
EXPOSE 9000

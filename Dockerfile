# Dockerfile

# Usar una imagen oficial de PHP con Apache
FROM php:8.2-apache

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar las dependencias necesarias incluyendo netcat-openbsd
RUN apt-get update && apt-get install -y netcat-openbsd \
    && docker-php-ext-install pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar los archivos de la aplicación
COPY . .

# Configura el DocumentRoot de Apache
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf

#Copiar configuración apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

# Habilitar el sitio de Apache
RUN a2ensite 000-default.conf

# Habilitar el módulo rewrite de Apache (necesario para Laravel)
RUN a2enmod rewrite

# Reinicia Apache
RUN service apache2 restart

# Copiar el script de entrada
COPY docker-entrypoint.sh /usr/local/bin/

# Dar permisos de ejecución al script de entrada
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Instalar dependencias de Composer
RUN composer install

# Configura los permisos
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage/framework


# Exponer el puerto 80
EXPOSE 80

# Ejecutar el script de entrada
ENTRYPOINT ["docker-entrypoint.sh"]
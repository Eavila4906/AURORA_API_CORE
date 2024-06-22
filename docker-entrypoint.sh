#!/bin/bash

# Esperar a que la base de datos esté disponible
until nc -z -v -w30 db 3306; do
  echo 'Waiting for MySQL to be ready...'
  sleep 1
done

# Ejecutar migraciones
php artisan migrate

# Ejecutar passport
php artisan passport:install

# Ejecutar el servidor Apache
apache2-foreground

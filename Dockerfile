# docker/php/Dockerfile
FROM php:8.3-fpm

# Installe les bibliothèques nécessaires à pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

# Copier tout le projet dans le conteneur
COPY . /var/www/html/

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html



CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]
# docker/php/Dockerfile
FROM php:8.3-fpm

# Installe les bibliothèques nécessaires à pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html
COPY ./public /var/www/html/public

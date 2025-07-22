FROM php:8.2-fpm

# Install extensions
RUN apt-get update && apt-get install -y \
    libpq-dev unzip curl && \
    docker-php-ext-install pdo pdo_pgsql

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Installer les dépendances du projet
WORKDIR /var/www
COPY . /var/www
RUN composer install

# Droits
RUN chown -R www-data:www-data /var/www

EXPOSE 9000

# Démarrer le serveur intégré PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

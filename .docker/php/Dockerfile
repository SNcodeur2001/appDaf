# Dockerfile
FROM php:8.3-fpm

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    libpq-dev unzip curl git \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier uniquement les fichiers nécessaires pour installer les dépendances
COPY composer.json composer.lock ./

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader


# Copier tout le reste du code (routes, public/, src/, etc.)
COPY . .

# Changer les permissions
RUN chown -R www-data:www-data /var/www/html

# Lancer le serveur PHP interne (à adapter si tu utilises nginx/fpm)
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]

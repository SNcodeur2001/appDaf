# Utilise l'image officielle PHP avec FPM
FROM php:8.3-fpm

# Installe les bibliothèques nécessaires à PDO pour PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    unzip \
    curl \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Définir le répertoire de travail dans le conteneur
WORKDIR /var/www/html

# Copier tout le projet dans le conteneur
COPY . /var/www/html/

# Installer les dépendances PHP via Composer
RUN composer install --no-interaction --no-dev --prefer-dist

# Définir les permissions appropriées
RUN chown -R www-data:www-data /var/www/html

# Lancer le serveur PHP intégré en exposant le dossier public
CMD ["php", "-S", "0.0.0.0:80", "-t", "public"]

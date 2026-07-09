FROM php:8.2-apache

# 1. Installeer SQLite extensies
RUN apt-get update && apt-get install -y libsqlite3-dev && docker-php-ext-install pdo pdo_sqlite

# 2. Verander de ID van www-data naar 1000 (jouw WSL-ID)
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# 3. Maak de database-map aan en geef rechten binnen de container
RUN mkdir -p /var/www/html/HitData && chown -R www-data:www-data /var/www/html/HitData


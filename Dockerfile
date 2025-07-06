FROM php:8.2-apache

# Install ekstensi untuk PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && docker-php-ext-install pdo pdo_pgsql

# Aktifkan modul rewrite
RUN a2enmod rewrite

# Salin konfigurasi Apache
COPY apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Salin semua isi project ke dalam direktori web server
COPY src/ /var/www/html/

# Set working directory
WORKDIR /var/www/html/public

# Dockerfile

FROM php:8.2-apache

# Install ekstensi
RUN docker-php-ext-install pdo pdo_pgsql

# Salin kode ke dalam container
COPY ./src /var/www/html

# Ubah permission agar apache bisa akses
RUN chown -R www-data:www-data /var/www/html

# Aktifkan rewrite module (jika pakai .htaccess)
RUN a2enmod rewrite

# Salin konfigurasi apache (jika ada)
# COPY ./apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

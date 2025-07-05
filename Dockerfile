FROM php:8.2-apache

# 1. Install dependency untuk PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql

# 2. Copy project ke dalam container
COPY ./src /var/www/html

# 3. Ubah permission
RUN chown -R www-data:www-data /var/www/html

# 4. Aktifkan mod_rewrite (jika perlu)
RUN a2enmod rewrite

# 5. Expose port 80
EXPOSE 80

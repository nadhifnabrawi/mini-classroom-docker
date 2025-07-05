FROM php:8.1-fpm

# Install ekstensi dan dependencies PostgreSQL & lainnya
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_pgsql pgsql

WORKDIR /var/www/html

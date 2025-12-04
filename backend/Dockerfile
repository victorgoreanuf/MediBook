FROM php:8.2-fpm-alpine

# Install necessary extensions
RUN apk add --no-cache \
    postgresql-dev \
    autoconf \
    g++ \
    make \
    git

# Install PDO PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Install MongoDB AND Redis extensions
RUN pecl install mongodb redis \
    && docker-php-ext-enable mongodb redis  # <--- Added redis here

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

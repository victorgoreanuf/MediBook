# backend/Dockerfile

FROM php:8.2-fpm-alpine
  
  # Install necessary extensions
RUN apk add --no-cache \
postgresql-dev \
  # Needed for MongoDB extension installation
autoconf \
g++ \
make \
  # Needed for basic utilities
git
  
  # Install PDO PostgreSQL and MongoDB extension
RUN docker-php-ext-install pdo pdo_pgsql
RUN pecl install mongodb && docker-php-ext-enable mongodb
  
  # Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
  
  # Set working directory
WORKDIR /var/www
FROM php:8.2-fpm

# Update and install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libpq-dev \
    postgresql-client \
    libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql \
    && docker-php-ext-install intl \
    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

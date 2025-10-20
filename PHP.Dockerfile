FROM php:fpm

# Install PDO extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install GD (with JPEG, PNG, and Freetype support)
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev && \
    docker-php-ext-configure gd --with-jpeg --with-freetype && \
    docker-php-ext-install gd

# Install ssmtp
RUN apt-get update && apt-get install -y ssmtp 

# Install and enable Xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy custom PHP config files
COPY php.ini /usr/local/etc/php/conf.d/custom.ini
COPY ssmtp.conf /etc/ssmtp/ssmtp.conf

FROM php:fpm

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    ssmtp \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy custom php.ini
COPY php.ini /usr/local/etc/php/conf.d/custom.ini

COPY ssmtp.conf /etc/ssmtp/ssmtp.conf

RUN apt-get clean && rm -rf /var/lib/apt/lists/*
# FROM php:7.4-fpm
FROM php:8.1.0-fpm
COPY php.ini /usr/local/etc/php/

# install composer
RUN cd /usr/bin && curl -s http://getcomposer.org/installer | php && ln -s /usr/bin/composer.phar /usr/bin/composer
RUN apt-get update \
    && apt-get install -y \
    libicu-dev \
    zlib1g-dev \
    vim \
    libzip-dev \
    && docker-php-ext-install zip

RUN apt-get update \
    && apt-get install -y libpq-dev git unzip zlib1g-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql intl zip 

WORKDIR /var/www/html
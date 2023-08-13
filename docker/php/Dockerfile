FROM php:8.2-fpm

COPY php.ini /usr/local/etc/php/
COPY www.conf /usr/local/etc/php-fpm.d/

RUN apt-get update \
    && apt-get install -y \
    curl zip unzip

WORKDIR /var/www
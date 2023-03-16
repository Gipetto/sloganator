FROM php:8.2-apache

ENV SLOGANATOR_LOCAL_DOCKER=true

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini \
    && pear config-set php_ini /usr/local/etc/php/php.ini

# Composer prefers ZIP to pull packages
RUN apt update \
    && apt install -y libzip-dev zip \
    && docker-php-ext-install zip

# XDebug is used during testing
RUN pecl install xdebug-3.2.0 \
    && docker-php-ext-enable xdebug

RUN a2enmod rewrite
RUN a2enmod headers
RUN a2enmod proxy
RUN a2enmod proxy_http

RUN apt clean

CMD apache2-foreground
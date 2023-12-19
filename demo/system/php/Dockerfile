FROM php:8.2.7-fpm

RUN docker-php-ext-install pdo_mysql

ADD ./system/php/custom-php.ini /usr/local/etc/php/conf.d/custom-php.ini

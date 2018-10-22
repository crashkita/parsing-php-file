FROM php:7.2-apache

RUN apt-get update && \
    apt-get install -y git

# Подключаем модуль Apache Rewrite
RUN cd /etc/apache2/mods-enabled && \
    ln -s ../mods-available/rewrite.load

# XDebug
RUN pecl install xdebug-2.6.1 \
    && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Копируем конфигурацию сервера HTTP
COPY 000-default.conf /etc/apache2/sites-available/
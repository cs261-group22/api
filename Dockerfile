FROM composer:2.0.8 as build
WORKDIR /app
COPY . /app
RUN composer install

FROM php:7.4-apache

RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql

EXPOSE 80

COPY --from=build /app /var/www/
COPY deployment/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN chmod 777 -R /var/www/storage && \
    chown -R www-data:www-data /var/www && \
    a2enmod rewrite

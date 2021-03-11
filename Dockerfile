FROM composer:2.0.8 as build
WORKDIR /app
COPY . /app
RUN composer install --ignore-platform-reqs

FROM php:8.0-apache

RUN apt-get update \
    && apt-get install -y libpq-dev libwebp-dev libjpeg62-turbo-dev libpng-dev libxpm-dev libfreetype6-dev zlib1g-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && docker-php-ext-configure gd --with-gd --with-webp-dir --with-jpeg-dir \
           --with-png-dir --with-zlib-dir --with-xpm-dir --with-freetype-dir \
           --enable-gd-native-ttf \
    && docker-php-ext-install gd

EXPOSE 80

COPY --from=build /app /var/www/
COPY deployment/vhost.conf /etc/apache2/sites-available/000-default.conf

RUN chmod 777 -R /var/www/storage && \
    chown -R www-data:www-data /var/www && \
    a2enmod rewrite

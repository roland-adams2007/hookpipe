FROM php:8.3-apache

RUN apt-get update && apt-get install -y \
    libpq-dev \
    zip unzip git curl \
    && docker-php-ext-install pdo pdo_pgsql pgsql

COPY . /var/www/html
WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env || true
RUN php artisan key:generate || true

EXPOSE 80
CMD ["apache2-foreground"]
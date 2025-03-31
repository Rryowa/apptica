FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip pdo_mysql

RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L 'https://github.com/phpredis/phpredis/archive/5.3.7.tar.gz' -o redis-5.3.7.tar.gz \
    && tar xfz redis-5.3.7.tar.gz -C /usr/src/php/ext/redis --strip 1 \
    && rm redis-5.3.7.tar.gz \
    && docker-php-ext-install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data storage \
    && chown -R www-data:www-data bootstrap/cache

EXPOSE 9000


FROM php:8.2-fpm

RUN apt-get update && apt-get install -y dnsutils libcurl3-dev libxml++2.6-dev curl libzip-dev zip git libwebp-dev libonig-dev libpq-dev libz-dev \
    && docker-php-ext-install iconv mbstring pdo_pgsql zip curl exif xml

RUN apt-get install -y build-essential libssl-dev zlib1g-dev libpng-dev libjpeg-dev libfreetype6-dev
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install gd && \
    docker-php-ext-install sockets && \
    docker-php-ext-enable gd && \
    docker-php-ext-enable sockets && \
    pecl install xdebug && \
    docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ADD docker/php/php.ini /usr/local/etc/php/conf.d/40-custom.ini

WORKDIR /app

RUN chown -R www-data:www-data .

CMD ["php-fpm"]

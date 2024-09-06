FROM php:7.1-cli AS base
WORKDIR /app
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libssl-dev \
    && mkdir -p /usr/local/etc/ssl/certs \
    && curl https://curl.se/ca/cacert.pem --output /usr/local/etc/ssl/certs/cacert.pem \
    && rm -rf /var/lib/apt/lists/*

RUN echo "openssl.cafile=/usr/local/etc/ssl/certs/cacert.pem" >> /usr/local/etc/php/conf.d/cafile.ini

FROM base AS composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

FROM base AS debug
RUN pecl channel-update pecl.php.net && \
    pecl install xdebug-2.9.8 && \
    docker-php-ext-enable xdebug && \
    echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
WORKDIR /app

CMD ["bash"]
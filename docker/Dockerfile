FROM php:7.2-cli AS base
WORKDIR /app
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

FROM base AS composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version


FROM base AS php

COPY --from=composer /usr/local/bin/composer /usr/local/bin/composer
WORKDIR /app

CMD ["bash"]

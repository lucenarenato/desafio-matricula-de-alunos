# Use a imagem base do PHP 8.2 FPM Alpine
FROM php:8.2-fpm-alpine

# Variáveis de ambiente
ENV GROUP_ID=1000 \
    USER_ID=1000 \
    USER_NAME=www \
    GROUP_NAME=www \
    WORKDIR=/var/www/html \
    DOCUMENT_ROOT=${WORKDIR} \
    LARAVEL_PROCS_NUMBER=1 \
    TZ=America/Sao_Paulo

# Configurar o fuso horário
RUN apk add --no-cache tzdata && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    echo $TZ > /etc/timezone

# Instalar dependências necessárias e ferramentas
RUN apk --update add --no-cache \
    wget \
    curl \
    git \
    grep \
    gcc \
    build-base \
    libmemcached-dev \
    libxml2-dev \
    imagemagick-dev \
    pcre-dev \
    libtool \
    make \
    autoconf \
    automake \
    g++ \
    nasm \
    cyrus-sasl-dev \
    libgsasl-dev \
    supervisor \
    libpq-dev \
    libpng-dev \
    zlib-dev \
    vips-dev \
    $PHPIZE_DEPS

RUN apk update && apk add oniguruma-dev
RUN docker-php-ext-install -j$(nproc) mbstring

RUN apk --update add --no-cache \
    shadow \
    hiredis \
    libevent \
    libevent-dev \
    openssl-dev \
    gmp-dev \
    icu-dev \
    && \
    docker-php-ext-install \
    pcntl \
    mbstring \
    pdo \
    pdo_mysql \
    xml \
    sockets \
    gmp \
    bcmath \
    intl \
    && \
    mkdir -p /var/www/html

# Installing PHP
RUN apk add --no-cache php82 \
    php82-common \
    php82-fpm \
    php82-pdo \
    php82-opcache \
    php82-zip \
    php82-phar \
    php82-iconv \
    php82-cli \
    php82-curl \
    php82-openssl \
    php82-mbstring \
    php82-tokenizer \
    php82-fileinfo \
    php82-json \
    php82-xml \
    php82-xmlwriter \
    php82-simplexml \
    php82-dom \
    php82-pdo_mysql \
    php82-pdo_sqlite \
    php82-tokenizer \
    php82-pecl-redis

RUN ln -s /usr/bin/php82 /usr/bin/php

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Instalar extensões PHP comuns
RUN docker-php-ext-install mysqli pdo pdo_mysql xml

# Instalar a extensão PCNTL
RUN docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-install pcntl

# Instalar a extensão Sockets
RUN docker-php-ext-install sockets

# Instalar a extensão GD
RUN apk add --no-cache freetype-dev libjpeg-turbo-dev libpng-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd intl

# Instalar extensões PECL
RUN pecl channel-update pecl.php.net && \
    pecl install memcached imagick && \
    docker-php-ext-enable memcached imagick

# Criar grupo e usuário
RUN addgroup -g $GROUP_ID $GROUP_NAME && \
    adduser -D -u $USER_ID -G $GROUP_NAME $USER_NAME -s /bin/sh

# Add non-root user, prevents files being created with root permissions on host machine
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}
# RUN groupmod -g ${PGID} www-data && \
#     usermod -u ${PUID} www-data

# USER www-data
# Configurar diretórios de trabalho
WORKDIR $WORKDIR
RUN mkdir -p storage bootstrap/cache && \
    chown -R $USER_NAME:$GROUP_NAME storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Adicionar o cron job ao crontab (opcional, se você realmente precisa)
RUN echo "0 * * * * chmod -R 775 /var/www/html/storage" >> /etc/crontabs/root

# Limpar cache do APK
RUN rm -rf /var/cache/apk/*

# Entrypoint para iniciar o PHP e o cron
#CMD ["php-fpm"]
EXPOSE 6001

CMD ["php", "artisan", "websockets:serve"]

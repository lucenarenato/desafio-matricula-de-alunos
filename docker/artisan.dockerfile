FROM php

COPY --from=composer /usr/bin/composer /usr/bin/composer

#
#--------------------------------------------------------------------------
# Image Setup
#--------------------------------------------------------------------------
#

FROM php:8.2-fpm

# Set Environment Variables
ENV DEBIAN_FRONTEND=noninteractive

#
#--------------------------------------------------------------------------
# Software's Installation
#--------------------------------------------------------------------------
#
# Installing tools and PHP extentions using "apt", "docker-php", "pecl",
#

# Install "curl", "libmemcached-dev", "libpq-dev", "libjpeg-dev",
#         "libpng-dev", "libfreetype6-dev", "libssl-dev", "libmcrypt-dev",
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libc6 \
    zip \
    unzip \
    supervisor \
    htop

# mix
RUN apt-get update \
    && apt-get install -y build-essential zlib1g-dev default-mysql-client curl gnupg procps vim git unzip libzip-dev libpq-dev htop \
    && docker-php-ext-install zip pdo_mysql pdo_pgsql pgsql
# Supervisor install
RUN apt-get install -y supervisor;
# Schedule Configuration
RUN apt-get update -y \
    && apt-get install -y cron ; #

RUN set -eux; \
    apt-get update; \
    apt-get upgrade -y; \
    apt-get install -y --no-install-recommends \
    curl \
    libmemcached-dev \
    libz-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libssl-dev \
    libwebp-dev \
    libxpm-dev \
    libmcrypt-dev \
    libonig-dev; \
    rm -rf /var/lib/apt/lists/*

RUN set -eux; \
    # Install the PHP pdo_mysql extention
    docker-php-ext-install pdo_mysql; \
    # Install the PHP pdo_pgsql extention
    docker-php-ext-install pdo_pgsql; \
    # Install the PHP gd library
    docker-php-ext-configure gd \
    --prefix=/usr \
    --with-jpeg \
    --with-webp \
    --with-xpm \
    --with-freetype; \
    docker-php-ext-install gd; \
    php -r 'var_dump(gd_info());'

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    python3-pip

RUN docker-php-ext-configure gd \
    --prefix=/usr \
    --with-jpeg \
    --with-webp \
    --with-xpm \
    --with-freetype; \
    docker-php-ext-install gd; \
    php -r 'var_dump(gd_info());'

RUN pip install youtube_transcript_api --break-system-packages

# Altera as permissões do diretório como root antes de mudar para www-data
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY supervisord.conf /etc/supervisord.conf
COPY start.sh /usr/local/bin/start
RUN chmod u+x /usr/local/bin/start
#RUN chmod +x /usr/local/bin/start
RUN ls -l /usr/local/bin/

# Alteramos para o usuário www-data para rodar o start.sh com permissões apropriadas
USER www-data

#SUSER root

CMD ["/usr/local/bin/start"]

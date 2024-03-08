FROM php:8.1-fpm

RUN apt-get update \
  && apt-get install -y \
    libicu-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
#    libmcrypt-dev \
    libpng-dev \
    libxpm-dev \
    libxslt1-dev \
    libzip-dev \
    libde265-dev \
    git \
    curl \
    nano \
    unzip
  
RUN docker-php-ext-configure gd  --with-freetype --with-jpeg;\
    docker-php-ext-install \
    opcache \
    bcmath \
    gd \
    intl \
    pdo_mysql \
    soap \
    xsl \
    sockets \
    zip
    

RUN pecl install --force xdebug \
  docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.2.10

RUN echo 'memory_limit = 8G\n\
max_execution_time = 1800\n\
upload_max_filesize = 40M\n\
post_max_size = 40M\n\
max_allowed_packet=64M\n\
max_input_vars = 3000\n\
zlib.output_compression = On' >> /usr/local/etc/php/php.ini

WORKDIR /var/www/html

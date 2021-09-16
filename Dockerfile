FROM php:8.0.3-cli-buster

WORKDIR /app

# Install php and packages
RUN apt-get update && apt-get install -y \
    curl \
    wget \
    git \
    zip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
	libpng-dev \
	libonig-dev \
	libzip-dev \
	libmcrypt-dev \
	libyaml-dev \
	libcurl3-dev \
	libsnmp-dev \
	libxml2-dev \
	libedit-dev \
    libmemcached-dev  \
    zlib1g-dev


RUN docker-php-ext-install -j$(nproc) iconv mbstring zip curl snmp opcache sockets
RUN pecl install memcached \
    && pecl install yaml \
    && pecl install mcrypt \
    && docker-php-ext-enable memcached mcrypt yaml opcache sockets

#Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#Copy configuration and scripts
ADD docker/php.ini /usr/local/etc/php/conf.d/40-custom.ini

COPY ./ /app

RUN composer install || composer update && \
    ./vendor/bin/rr get -l /usr/bin && \
    docker-php-source delete \
    && apt-get autoremove --purge -y && \
    apt-get autoclean -y && \
    apt-get clean -y

CMD ["/usr/bin/rr", "serve", "-c", "/app/.rr.yaml"]

FROM php:8.2.12-apache-bookworm

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        curl \
        libbz2-dev \
        libfreetype6-dev \
        libgmp-dev \
        libicu-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        bcmath \
        bz2 \
        exif \
        gd \
        gmp \
        intl \
        mbstring \
        mysqli \
        pdo_mysql \
        zip \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j"$(nproc)" opcache

COPY docker/site/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/site/php-wl.ini /usr/local/etc/php/conf.d/zz-wl.ini
COPY site/ /var/www/html/
COPY docker/site/entrypoint.sh /usr/local/bin/wl-entrypoint

RUN chmod +x /usr/local/bin/wl-entrypoint \
    && mkdir -p \
        /var/www/html/writable/cache \
        /var/www/html/writable/logs \
        /var/www/html/writable/session \
        /var/www/html/writable/uploads \
    && chown -R www-data:www-data /var/www/html/writable

ENTRYPOINT ["wl-entrypoint"]
CMD ["apache2-foreground"]

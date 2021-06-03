FROM existenz/webstack:7.4 AS base

RUN apk add --no-cache \
    argon2 \
    curl \
    shadow \
    icu \
    php7-common \
    php7 \
    php7-fpm \
    php7-sodium \
    php7-ctype \
    php7-curl  \
    php7-redis \
    php7-dom  \
    php7-gd \
    php7-iconv \
    php7-intl \
    php7-json \
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    php7-pdo \
    php7-pdo_pgsql \
    php7-pgsql \
    php7-phar \
    php7-session \
    php7-tokenizer \
    php7-xmlwriter \
    php7-xmlreader \
    php7-simplexml \
    php7-fileinfo \
    php7-xml \
    php7-zip \
    php7-zlib

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY ./docker/ /

FROM base AS dev
ARG USERID=101
ARG GROUPID=101

RUN usermod -u ${USERID} php \
 && groupmod -o -g ${GROUPID} php \
 && chown -R php:php /www

FROM base AS prod

COPY --chown=php:php . /www
RUN composer install \
 && chown -R php:php /www/vendor

FROM existenz/webstack:8.0 AS base

RUN apk add --no-cache \
    argon2 \
    curl \
    shadow \
    icu \
    php8-common \
    php8 \
    php8-fpm \
    php8-sodium \
    php8-ctype \
    php8-curl  \
    php8-redis \
    php8-dom  \
    php8-gd \
    php8-iconv \
    php8-intl \
    php8-json \
    php8-mbstring \
    php8-opcache \
    php8-openssl \
    php8-pdo \
    php8-pdo_pgsql \
    php8-pgsql \
    php8-phar \
    php8-session \
    php8-tokenizer \
    php8-xmlwriter \
    php8-xmlreader \
    php8-simplexml \
    php8-fileinfo \
    php8-xml \
    php8-zip \
    php8-zlib

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

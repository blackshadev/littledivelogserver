FROM existenz/webstack:8.2-edge AS base

RUN apk add --no-cache \
    argon2 \
    curl \
    shadow

RUN apk -U --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/main/ add \
    icu-libs \
    && apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ \
    php82 \
    php82-common \
    php82-ctype \
    php82-curl  \
    php82-dom  \
    php82-fileinfo \
    php82-fpm \
    php82-iconv \
    php82-intl \
    php82-json \
    php82-mbstring \
    php82-opcache \
    php82-openssl \
    php82-pdo \
    php82-pdo_pgsql \
    php82-pgsql \
    php82-phar \
    php82-redis \
    php82-session \
    php82-simplexml \
    php82-sodium \
    php82-tokenizer \
    php82-xml \
    php82-xmlreader \
    php82-xmlwriter \
    php82-zip \
    php82-zlib

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY ./docker/ /
RUN ln /usr/bin/php82 /usr/bin/php


FROM base AS dev

ARG WWWGROUP

RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/edge/community/ \
    php82-xdebug

COPY ./docker/dev-files/ /


RUN groupmod -o -g ${WWWGROUP} php \
 && chown -R php:php /www

FROM base AS prod

COPY --chown=php:php . /www
RUN composer install --ansi \
 && chown -R php:php /www/vendor

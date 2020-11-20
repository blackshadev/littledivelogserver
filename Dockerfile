FROM alpine:3.11

ARG USERID
ARG GROUPID

# Add PHP 7.4 repository
ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub
RUN apk --update add ca-certificates && \
    echo "https://dl.bintray.com/php-alpine/v3.11/php-7.4" >> /etc/apk/repositories

RUN apk update \
    && export PHP_VERSION=$(apk search php7-common | sed 's/php7-common-//') \
    && apk add -U --no-cache \
    curl \
    shadow \
    php7=$PHP_VERSION \
    php7-apcu \
    php7-fpm=$PHP_VERSION \
    php7-ctype=$PHP_VERSION \
    php7-curl=$PHP_VERSION \
    php7-dom=$PHP_VERSION \
    php7-gd=$PHP_VERSION \
    php7-iconv=$PHP_VERSION \
    php7-intl=$PHP_VERSION \
    php7-json=$PHP_VERSION \
    php7-mbstring=$PHP_VERSION \
    php7-opcache=$PHP_VERSION \
    php7-openssl=$PHP_VERSION \
    php7-pdo=$PHP_VERSION \
    php7-pdo_pgsql=$PHP_VERSION \
    php7-phar=$PHP_VERSION \
    php7-session=$PHP_VERSION \
    php7-xml=$PHP_VERSION \
    php7-zip=$PHP_VERSION \
    php7-zlib=$PHP_VERSION \
    && ln -s /usr/sbin/php-fpm7 /usr/sbin/php-fpm \
    && addgroup -S php \
    && adduser -S -G php php \
    && rm -rf /var/cache/apk/*

RUN usermod -u ${USERID} php \
 && groupmod -o -g ${GROUPID} php

# Set working directory
WORKDIR /www

EXPOSE 9000

ENTRYPOINT php-fpm --nodaemonize

FROM alpine:3.11

ARG USERID
ARG GROUPID

ADD https://dl.bintray.com/php-alpine/key/php-alpine.rsa.pub /etc/apk/keys/php-alpine.rsa.pub

RUN apk --update-cache add ca-certificates && \
    echo "https://dl.bintray.com/php-alpine/v3.11/php-7.4" >> /etc/apk/repositories

RUN apk update \
 && export PHP_VERSION=$(apk search php7-common | sed 's/php7-common-//') \
 && apk add --update-cache \
    argon2 \
    curl \
    shadow \
    icu \
    php7-common \
    php7=$PHP_VERSION \
    php7-fpm=$PHP_VERSION \
    php7-sodium \
    php7-ctype \
    php7-curl  \
    php7-redis \
    php7-dom  \
    php7-gd \
    php7-iconv \
    php7-intl=$PHP_VERSION \
    php7-json \
    php7-mbstring \
    php7-opcache \
    php7-openssl \
    php7-pdo \
    php7-pdo_pgsql \
    php7-pdo_pgsql \
    php7-phar=$PHP_VERSION \
    php7-session \
    php7-xml \
    php7-zip \
    php7-zlib \
    && ln -s /usr/sbin/php-fpm7 /usr/sbin/php-fpm \
    && ln -s /usr/bin/php7 /usr/bin/php \
    && addgroup -S php \
    && adduser -S -G php php \
    && rm -rf /var/cache/apk/*

    RUN usermod -u ${USERID} php \
 && groupmod -o -g ${GROUPID} php

# Set working directory
RUN mkdir -p /var/www \
   chown php:php /var/www

COPY docker-compose/php-fpm /etc/php7/php-fpm.d/

WORKDIR /var/www

STOPSIGNAL SIGQUIT
EXPOSE 9000

CMD ["php-fpm"]

FROM php:7.4.19-fpm-alpine3.13
ENV HOME /root
WORKDIR /tmp
RUN apk --no-cache update && \
    apk add --no-cache libpng libjpeg-turbo libjpeg-turbo-dev freetype freetype-dev libpng-dev iproute2 && \
    docker-php-ext-configure gd \
    --with-freetype=/usr/include/ \
    --with-jpeg=/usr/include/

RUN NUMPROC=$(grep -c ^processor /proc/cpuinfo 2>/dev/null || 1) \
    && docker-php-ext-install -j${NUMPROC} gd

RUN docker-php-ext-install pdo
RUN adduser -D -H  -g "" -s /sbin/nologin FLAG_A7D3C0BA0F && echo "FLAG_A7D3C0BA0F:FA0B7E2850" | chpasswd

WORKDIR $HOME
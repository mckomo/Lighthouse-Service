################################################################################
# Base image
################################################################################

FROM php:7.1-alpine

WORKDIR /tmp

RUN apk --update add curl git && rm /var/cache/apk/*
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

RUN mkdir -p /var/www/lighthouse
WORKDIR /var/www/lighthouse

################################################################################
# Entrypoint
################################################################################

CMD ["top", "-d", "30"]
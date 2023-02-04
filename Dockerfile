FROM php:8.1 as php

RUN apt-get update -y
RUN apt-get install -y unzip libpq-dev libcurl4-gnutls-dev
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pgsql pdo_pgsql

WORKDIR /application
COPY . .

COPY --from=composer:2.3.5 /usr/bin/composer /usr/bin/composer

ENV PORT=8000

ENTRYPOINT [ "Docker/entrypoint.sh" ]

# ==============================================================================
#  node
FROM node:14-alpine as node

WORKDIR /application
COPY . .

RUN npm install --global cross-env
RUN npm install

VOLUME /application/node_modules
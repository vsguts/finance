FROM php:8.0.3-fpm-alpine3.13

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN apk add npm
RUN npm install -g less

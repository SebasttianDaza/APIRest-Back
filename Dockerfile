FROM php:8.1.17-apache

RUN apt-get update && \
  docker-php-ext-install mysqli pdo pdo_mysql

# Install dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install git
RUN apt-get install -y git
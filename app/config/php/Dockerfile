FROM php:8.1.17-fpm

# Arguments defined in docker-compose.yml
ARG user=user1
ARG uid=1000

RUN apt-get update && apt-get install -y

ENV COMPOSER_ALLOW_SUPERUSER=1

# Create system user to run Composer 
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
	chown -R $user:$user /home/$user 

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install mysqli pdo pdo_mysql 
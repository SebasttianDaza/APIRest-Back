FROM php:8.1.17-fpm

# Arguments defined in docker-compose.yml
ARG user=user1
ARG uid=1000

# Install and update system dependencies
RUN apt-get update && apt-get install -y \
	build-essential \
	libpng-dev \
	libjpeg62-turbo-dev \
	libfreetype6-dev \
	locales \
	zip \
	jpegoptim optipng pngquant gifsicle \
	vim \
	unzip \
	git \
	curl \
	libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

ENV COMPOSER_ALLOW_SUPERUSER=1

# Create system user to run Composer 
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
	chown -R $user:$user /home/$user 

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql 

EXPOSE 9000
CMD ["php-fpm"]
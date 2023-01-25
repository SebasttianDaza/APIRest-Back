FROM ubuntu:20.04

USER root

RUN apt update && apt-get update && apt upgrade -y

RUN apt install software-properties-common -y && add-apt-repository ppa:ondrej/php

RUN apt update && apt install php8.1 -y 

RUN php8.1-common php8.1-mysql php8.1-xml php8.1-xmlrpc php8.1-curl php8.1-gd php8.1-imagick php8.1-cli php8.1-dev php8.1-imap php8.1-mbstring php8.1-opcache php8.1-soap php8.1-zip php8.1-redis php8.1-intl -y

RUN apt-get install git -y

# Set the timezone and set the locale
RUN export 'LC_ALL="es_CO.utf8"' && export 'LC_CTYPE="es_CO.utf8"'
RUN apt-get install language-pack-en-base -y && dpkg-reconfigure locales
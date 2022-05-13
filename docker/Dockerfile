FROM ubuntu:21.10

WORKDIR /app

# Install php
RUN apt-get update && apt-get install -y software-properties-common && \
    add-apt-repository -y ppa:ondrej/php && \
    apt-get update && apt-get install -y \
        php8.1 \
        php8.1-bcmath \
        php8.1-curl \
        php8.1-dom \
        php8.1-mbstring \
        php8.1-mysql \
        curl \
        unzip

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy apache config files
COPY apache/apache2.conf /etc/apache2/apache2.conf
COPY apache/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf
#COPY apache/sites-available/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
#COPY apache/conf-available/ssl-params.conf /etc/apache2/conf-available/ssl-params.conf

# Enable Apache modules, sites and configs
RUN a2enmod rewrite headers ssl
#RUN a2ensite default-ssl && a2enconf ssl-params

# Give permission to App Folder
RUN chmod -R 777 /app

EXPOSE 80
CMD ["apachectl", "-D",  "FOREGROUND"]
FROM php:7.4-apache

COPY . /var/www/html/project

RUN ln -snf /usr/share/zoneinfo/Europe/Lisbon /etc/localtime && echo Europe/Lisbon > /etc/timezone
RUN apt-get update
RUN apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql exif pcntl bcmath gd

#install composer, which is dependency manager for laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#restart apache server and enable url rewrite mode
RUN service apache2 stop && a2enmod rewrite

#add virtual host configuration of the site
ADD 000-default.conf /etc/apache2/sites-available/000-default.conf

#Set the ENV vars
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_RUN_DIR /var/www/html/project

ADD entrypoint.sh /root/entrypoint.sh
RUN chmod 777 /root/entrypoint.sh
ENTRYPOINT /root/entrypoint.sh

WORKDIR /var/www/html/project

EXPOSE 80

# RUN DOCKER CONTAINER
# minikube start
# minikube docker-env
# kubectl run <NAME> --image=<IMAGE> --port=80
# kubectl expose deployment gateway --type="LoadBalancer"
# minikube service <NAME> --url
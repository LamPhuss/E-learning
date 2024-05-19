FROM php:8.0-apache
WORKDIR /var/www/html
RUN apt-get update -y && apt-get install -y libmariadb-dev && apt-get install -y sendmail libpng-dev
RUN docker-php-ext-install mysqli
RUN a2enmod headers
RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-install gd
COPY ./E-learning/configs/apache2.conf /etc/apache2/apache2.conf
COPY ./E-learning/configs/000-default.conf /etc/apache2/sites-available/000-default.conf

COPY ./E-learning/configs/apache2.conf /var/www/html/apache2.conf
COPY ./E-learning/configs/000-default.conf /var/www/html/000-default.conf

# Install git and other packaes
RUN apt-get update && apt-get install -y --force-yes --no-install-recommends \
    git \
    && rm -rf /var/lib/apt/lists/*

# config permission
RUN chown -R root:www-data /var/www/html
RUN chmod 750 /var/www/html
RUN find . -type f -exec chmod 640 {} \;
RUN find . -type d -exec chmod 750 {} \;

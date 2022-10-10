FROM php:8.1-apache

ARG BUILD_ARGUMENT_ENV=dev
ENV ENV=$BUILD_ARGUMENT_ENV
ENV APP_HOME /var/www/html
ARG HOST_UID=1000
ARG HOST_GID=1000
ENV USERNAME=www-data
ARG INSIDE_DOCKER_CONTAINER=1
ENV INSIDE_DOCKER_CONTAINER=$INSIDE_DOCKER_CONTAINER

RUN if [ "$BUILD_ARGUMENT_ENV" = "default" ]; then echo "Set BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev" && exit 2; \
    elif [ "$BUILD_ARGUMENT_ENV" = "dev" ]; then echo "Building development environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "test" ]; then echo "Building test environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "staging" ]; then echo "Building staging environment."; \
    elif [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then echo "Building production environment."; \
    else echo "Set correct BUILD_ARGUMENT_ENV in docker build-args like --build-arg BUILD_ARGUMENT_ENV=dev. Available choices are dev,test,staging,prod." && exit 2; \
    fi

RUN apt-get update && apt-get upgrade -y && apt-get install -y \
      nano \
      git \
      unzip \
      libicu-dev \
      zlib1g-dev \
      libxml2 \
      libxml2-dev \
      libreadline-dev \
      supervisor \
      sudo \
      libzip-dev \
      wget \
      librabbitmq-dev \
    && pecl install amqp \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure intl \
    && docker-php-ext-install \
      pdo_mysql \
      sockets \
      zip \
    && docker-php-ext-enable amqp \
    && rm -rf /tmp/* \
    && rm -rf /var/list/apt/* \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

RUN a2dissite 000-default.conf
RUN rm -r $APP_HOME

RUN mkdir -p $APP_HOME/public && \
    mkdir -p /home/$USERNAME && chown $USERNAME:$USERNAME /home/$USERNAME \
    && usermod -o -u $HOST_UID $USERNAME -d /home/$USERNAME \
    && groupmod -o -g $HOST_GID $USERNAME \
    && chown -R ${USERNAME}:${USERNAME} $APP_HOME

COPY ./docker/general/symfony.conf /etc/apache2/sites-available/symfony.conf
COPY ./docker/general/symfony-ssl.conf /etc/apache2/sites-available/symfony-ssl.conf
RUN a2ensite symfony.conf && a2ensite symfony-ssl
COPY ./docker/$BUILD_ARGUMENT_ENV/php.ini /usr/local/etc/php/php.ini

RUN a2enmod rewrite
RUN a2enmod ssl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN chmod +x /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/private/ssl-cert-snakeoil.key -out /etc/ssl/certs/ssl-cert-snakeoil.pem -subj "/C=AT/ST=Vienna/L=Vienna/O=Security/OU=Development/CN=example.com"

WORKDIR $APP_HOME

USER ${USERNAME}

COPY --chown=${USERNAME}:${USERNAME} . $APP_HOME/

RUN if [ "$BUILD_ARGUMENT_ENV" = "dev" ] || [ "$BUILD_ARGUMENT_ENV" = "test" ]; then COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress; \
    else export APP_ENV=$BUILD_ARGUMENT_ENV && COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader --no-interaction --no-progress --no-dev; \
    fi

RUN if [ "$BUILD_ARGUMENT_ENV" = "staging" ] || [ "$BUILD_ARGUMENT_ENV" = "prod" ]; then composer dump-env $BUILD_ARGUMENT_ENV; \
    fi

USER root
FROM alexmasterov/alpine-php:7.2

COPY . /app

RUN apk add --update git 

RUN apk add --update curl

WORKDIR /app

RUN curl --silent --show-error https://getcomposer.org/installer | php

RUN php composer.phar install --no-dev

EXPOSE 5672 3306

ENTRYPOINT php worker.php

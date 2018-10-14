FROM disc/php-amqp

COPY . /srv/app

RUN apt-get update && \
    apt-get install -y --no-install-recommends git

WORKDIR /srv/app

RUN curl --silent --show-error https://getcomposer.org/installer | php

RUN php composer.phar install --no-dev

EXPOSE 5672

ENTRYPOINT php worker.php

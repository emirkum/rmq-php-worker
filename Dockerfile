FROM disc/php-amqp

COPY . /srv/app

RUN apt-get update && \
    apt-get install -y --no-install-recommends git

WORKDIR /srv/app

RUN curl --silent --show-error https://getcomposer.org/installer | php

RUN php composer.phar install --no-dev

<<<<<<< HEAD:Dockerfile
ENTRYPOINT service rabbitmq-server start && php worker.php
=======
EXPOSE 5672

ENTRYPOINT php worker.php
>>>>>>> dockerfix:DockerFile

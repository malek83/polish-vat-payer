FROM php:7.1.3-cli

ARG SOURCE_DIR=/source

RUN mkdir ${SOURCE_DIR}

RUN apt-get update
RUN apt-get install -y libxml2-dev git zip

RUN docker-php-ext-install soap

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR ${SOURCE_DIR}

ENTRYPOINT ["tail", "-f", "/dev/null"]
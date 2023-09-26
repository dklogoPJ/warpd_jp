FROM romeoz/docker-apache-php:5.6

RUN apt-get update \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/app/

COPY ./app ./

#RUN a2enmod rewrite
RUN chmod -R 777 ./app/tmp

EXPOSE 80 443

CMD ["/sbin/entrypoint.sh"]

# Autor: Daniel Benjamin Perez Morales
# GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
# GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
# Correo electrónico: danielperezdev@proton.me

# Autor: Daniel Benjamin Perez Morales
# GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
# GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
# Correo electrónico: danielperezdev@proton.me

ARG TAG=8.4-apache-bullseye

# Stage 1: obtener dockerize
FROM jwilder/dockerize AS dockerize

# Stage 2: imagen final PHP + Apache
FROM php:${TAG}

# Instalación de dependencias, extensiones y dockerize
RUN apt-get update && \
    apt-get install -y \
        libpq-dev \
        tini \
        $PHPIZE_DEPS && \
    docker-php-ext-install pdo_pgsql && \
    pecl install redis mongodb && \
    docker-php-ext-enable redis mongodb && \
    mkdir -p /var/www/html/logs && \
    touch /var/www/html/logs/access.log /var/www/html/logs/error.log && \
    useradd -m d4nitrix13 && \
    rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copiar el código de la app con el propietario correcto
COPY --chown=d4nitrix13:d4nitrix13 ./ ./

# Sustituir la configuración de Apache
RUN ["mv", "./config/apache2.conf", "/etc/apache2/apache2.conf"]

# Copiar dockerize desde el stage anterior (binario confirmado en /bin)
COPY --from=dockerize /bin/dockerize /usr/local/bin/dockerize

# HEALTHCHECK usando el nombre del servicio en lugar de la IP
HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 \
    CMD [ "dockerize", "-wait", "http://app:80", "-stdout", "/var/www/html/logs/access.log", "-stderr", "/var/www/html/logs/error.log", "-wait-retry-interval", "1s", "-timeout", "10s" ]

ENTRYPOINT ["tini", "--"]

EXPOSE 80

SHELL ["/bin/bash", "-c"]
STOPSIGNAL SIGTERM

USER d4nitrix13

LABEL maintainer="Daniel Benjamin Perez Morales" \
      email="danielperezdev@proton.me" \
      nickname="D4nitrix13"

ENV APACHE_RUN_DIR=/var/run/apache2 \
    APACHE_LOCK_DIR=/var/lock/apache2 \
    APACHE_LOG_DIR=/var/log/apache2 \
    APACHE_PID_FILE=/var/run/apache2/apache2.pid \
    APACHE_RUN_USER=www-data \
    APACHE_RUN_GROUP=www-data

CMD ["apache2", "-D", "FOREGROUND"]

# Foros
# https://serverfault.com/questions/558283/apache2-config-variable-is-not-defined
# https://stackoverflow.com/questions/42654694/enable-php-apache2
# https://stackoverflow.com/questions/60370020/apache-is-running-a-threaded-mpm-but-your-php-module-is-not-compiled-to-be-thre

# Docker Hub: Apache
# https://hub.docker.com/_/httpd

# Dependencies App (Execute root)
# apt-get update && apt-get install -y libpq-dev
# docker-php-ext-install pdo_pgsql

# Command Production
# docker container exec -itePGPASSWORD=root DatabaseLocalDrive psql -hlocalhost -Upostgres -p5432 -f /App/sql/setup.sql

# Command Developer
# docker run -itdePOSTGRES_PASSWORD=root -p5432:5432 --expose 5432 --network bridge --name DatabaseLocalDrive postgres:latest

# Foros
# https://stackoverflow.com/questions/24659300/how-to-use-docker-images-filter#24659756

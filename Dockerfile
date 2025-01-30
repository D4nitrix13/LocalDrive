# Autor: Daniel Benjamin Perez Morales
# GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
# GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
# Correo electrónico: danielperezdev@proton.me

ARG Tag=8.4-apache-bullseye
FROM php:${Tag}
RUN  apt-get update && \
    apt-get install -y libpq-dev tini && \
    docker-php-ext-install pdo_pgsql && \
    mkdir -p /var/www/html/logs; touch /var/www/html/logs/access.log /var/www/html/logs/error.log
COPY --chown=d4nitrix13:d4nitrix13 ./ ./
RUN [ "mv", "./conf/apache2.conf", "/etc/apache2/apache2.conf" ]
COPY --chown=d4nitrix13:d4nitrix13 --from=jwilder/dockerize /bin/dockerize /bin/dockerize
# CMD-SHELL: CMD dockerize -wait http://172.18.0.2:80 -stdout /var/www/html/logs/access.log -stderr /var/www/html/logs/access.log -wait-retry-interval 1s -timeout 10s
HEALTHCHECK --interval=30s --timeout=30s --start-period=5s --retries=3 \
    CMD [ "dockerize", "-wait", "http://172.18.0.3:80", "-stdout", "/var/www/html/logs/access.log", "-stderr", "/var/www/html/logs/access.log", "-wait-retry-interval", "1s", "-timeout", "10s" ]
ENTRYPOINT [ "tini", "--" ]
EXPOSE 80/tcp
SHELL ["/bin/bash", "-c"]
STOPSIGNAL SIGTERM
RUN useradd -m d4nitrix13
USER d4nitrix13
LABEL maintainer="Daniel Benjamin Perez Morales"
LABEL email="danielperezdev@proton.me"
LABEL nickname="D4nitrix13"
ENV APACHE_RUN_DIR=/var/run/apache2
ENV APACHE_LOCK_DIR=/var/lock/apache2
ENV APACHE_LOG_DIR=/var/log/apache2
ENV APACHE_PID_FILE=/var/run/apache2/apache2.pid
ENV APACHE_RUN_USER=www-data
ENV APACHE_RUN_GROUP=www-data
CMD [ "apache2", "-D", "FOREGROUND" ]

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

FROM ubuntu:14.04

RUN apt-get update

##
# Base
##

RUN apt-get install -y python-setuptools git curl

##
# PHP & nginx
##

## Install required packages
RUN apt-get install -y php5-cli php5-fpm nginx php5-mysql php5-curl

## Patch-up the fpm version of php.ini
RUN echo "cgi.fix_pathinfo = 0;" >> /etc/php5/fpm/php.ini

## Make sure nginx stays in the foreground
RUN echo "daemon off;" >> /etc/nginx/nginx.conf
ADD docker/nginx.conf /etc/nginx/sites-available/default

##
# Composer
##

RUN (curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer)

##
# Build.
##

RUN mkdir /share

## hack database host to dev machine
RUN echo "10.0.2.2  mysqlhost" >> /etc/hosts


## Copy all files from the local directory into the container's /app directory
ADD . /app

## Run `composer install` inside the container's /app directory
RUN cd /app && composer install --prefer-dist --no-progress --ignore-platform-reqs

## Announce that this container exposes port 80
## Note: it doesn't become accessible unless you 'expose' it during `docker run` with -P
EXPOSE 80

## Start the container's single process (php5-fpm in background, nginx in foreground)
CMD ["sh", "-c", "service php5-fpm start && nginx"]

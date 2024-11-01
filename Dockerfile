FROM php:7.4-apache
RUN apt-get update && apt upgrade -y
RUN docker-php-ext-install mysqli

# COPY ./app/my-site.conf /etc/apache2/sites-available/my-site.conf

RUN echo "ServerName localhost\nServerName sev-1.cse356.compas.cs.stonybrook.edu\nHeader setifempty X-CSE356 66d0f3556424d34b6b77c48f" >> /etc/apache2/apache2.conf &&\
    a2enmod headers &&\
    a2dissite 000-default &&\
    # a2ensite my-site &&\
    service apache2 restart
EXPOSE 80
EXPOSE 443

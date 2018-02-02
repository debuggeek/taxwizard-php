FROM php:7.0-apache

COPY *.php /var/www/html/fivestone/
COPY *.css /var/www/html/fivestone/
COPY *.html /var/www/html/fivestone/

COPY library/ /var/www/html/fivestone/library/

COPY library/mpdf6/ /var/www/html/fivestone/library/mpdf6/

COPY resources/ /var/www/html/fivestone/resources/

COPY services/ /var/www/html/fivestone/services/

RUN mkdir /var/www/html/fivestone/upload
RUN chown :www-data /var/www/html/fivestone/upload/

COPY admin/accountinfo.php.local /var/www/html/fivestone/library/accountinfo.php

COPY cli/ /var/www/html/fivestone/cli/

COPY admin/batchwatchdog.sh /usr/local/
RUN chmod +x /usr/local/batchwatchdog.sh

EXPOSE 80

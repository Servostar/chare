FROM richarvey/nginx-php-fpm:latest
# copy nginx config file
COPY nginx.conf /etc/nginx
# copy app files
COPY composer.json /var/www/html

# for testing only
COPY test /var/share

COPY php /var/www/html
COPY .ignore /srv
COPY master.css /var/www/html
COPY vendor /var/www/html/vendor
ENV SHARE_PATH=/var/share
ENV SERVER_NAME=example.com
ENV HOME_PAGE=https://home.example.com
ENV LEGAL_PAGE=https://legal.example.com
ENV IMPRESSUM_PAGE=https://impressum.example.com
WORKDIR "/var/www/html"
CMD ["/start.sh"]
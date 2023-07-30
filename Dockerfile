FROM richarvey/nginx-php-fpm:latest
# copy nginx config file
COPY nginx.conf /etc/nginx
# copy app files
COPY composer.json /var/www/html

# for testing only
COPY test /var/share

COPY index.php /var/www/html
COPY common.php /var/www/html
COPY master.css /var/www/html
COPY vendor /var/www/html/vendor
WORKDIR "/var/www/html"
CMD ["/start.sh"]
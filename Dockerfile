FROM richarvey/nginx-php-fpm:latest

RUN mkdir /var/share/

# setup config files
COPY .ignore /srv/config/
COPY examples/repos.tsv /srv/config

# setup autofetch via custom script
COPY sync.sh /var/scripts/
RUN chmod +x /var/scripts/sync.sh

# copy essential files for web service
COPY nginx.conf /etc/nginx
COPY php /var/www/html
COPY js /var/www/html
COPY master.css /var/www/html
COPY composer.json /var/www/html
COPY vendor /var/www/html/vendor

COPY start.sh /
RUN chmod +x /start.sh

ENV SHARE_PATH /var/share

WORKDIR "/var/www/html"
CMD ["/start.sh"]
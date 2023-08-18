FROM richarvey/nginx-php-fpm:latest

# for testing only
COPY test /var/share

# setup config files
COPY .ignore /srv/config/
COPY repos.tsv /srv/config/

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

# set default values for environment variables
ENV SHARE_PATH=/var/share
ENV SERVER_NAME=example.com
ENV HOME_PAGE=https://home.example.com
ENV LEGAL_PAGE=https://legal.example.com
ENV IMPRESSUM_PAGE=https://impressum.example.com

COPY start.sh /
RUN chmod +x /start.sh

WORKDIR "/var/www/html"
CMD ["/start.sh"]
# Create the TreasureHunt docker container

FROM node:21-alpine AS node
FROM php:8.3-fpm-alpine

# Update APK and get it ready
RUN apk upgrade && apk update

# Get the node stuff installed in the right places
RUN apk add --no-cache libstdc++ libgcc
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/include/node /usr/local/include/node
COPY --from=node /usr/local/share/man/man1/node.1 /usr/local/share/man/man1/node.1
COPY --from=node /usr/local/share/doc/node /usr/local/share/doc/node
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /opt/ /opt/
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm
RUN ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx
RUN ln -s /opt/yarn-$(ls /opt/ | grep yarn | sed 's/yarn-//')/bin/yarn /usr/local/bin/yarn
RUN ln -s /opt/yarn-$(ls /opt/ | grep yarn | sed 's/yarn-//')/bin/yarnpkg /usr/local/bin/yarnpkg

# Setup MudSlide
ENV GIT_SSL_NO_VERIFY 1
ENV MUDSLIDE_CACHE_FOLDER /usr/src/app/cache
RUN apk add --no-cache git
RUN git config --global url."https://github".insteadOf ssh://git@github
RUN mkdir /app
COPY mudslide/ /app
WORKDIR /app
RUN yarn install
RUN yarn build

# Install other software we may need
RUN apk add --no-cache openssh apache2 php-apache2 php-mysqli

# Install necessary extensions for MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Optional - Install PhpMyAdmin
RUN apk add --no-cache phpmyadmin
RUN chown -R apache:apache /etc/phpmyadmin
COPY ./server/phpmyadmin.php /etc/phpmyadmin/config.inc.php

# Install npm and then mudslide
RUN mkdir /root/.ssh/ ; ssh-keyscan github.com >> /root/.ssh/known_hosts 
RUN npm install -g mudslide

# Copy the HTML/PHP files from the html directory here to the Apache web server directory
RUN rm /var/www/localhost/htdocs/*
COPY html/* /var/www/html/
COPY server/httpd.conf /etc/apache2/httpd.conf

# CMD service apache2 start
CMD ["/usr/sbin/httpd", "-D", "FOREGROUND"]


# Expose port 80 to outside
EXPOSE 80




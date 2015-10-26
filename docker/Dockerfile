FROM debian:8.2

# add mongo apt repo
RUN apt-key adv --keyserver keyserver.ubuntu.com --recv 7F0CEB10  
RUN echo "deb http://repo.mongodb.org/apt/debian wheezy/mongodb-org/3.0 main" | tee /etc/apt/sources.list.d/mongodb-org-3.0.list  

RUN apt-get update
RUN apt-get dist-upgrade -y

# Install the relevant packages
RUN apt-get install php5-dev php5-cli php-pear mongodb-org -y  

RUN pecl install mongo


ENV SEARCH='; If you wish to have an extension loaded automatically, use the following'
ENV REPLACE='extension=mongo.so\n; If you wish to have an extension loaded automatically, use the following'
ENV FILEPATH="/etc/php5/cli/php.ini"
RUN sed -i "s:$SEARCH:$REPLACE:" $FILEPATH


# Install the cron service
RUN apt-get install cron -y

# Add our websites files to the default apache directory (/var/www)
# This should be as close to the last step as possible for faster rebuilds
ADD mongo-filesystem /root/mongo-filesystem

# Use the crontab file.
# The crontab file was already added when we added "project"
RUN crontab /root/mongo-filesystem/docker/crons.conf


# Execute the containers startup script which will start many processes/services
# The startup file was already added when we added "project"
CMD ["/bin/bash", "/root/mongo-filesystem/docker/startup.sh"]

#!/usr/bin/sh

apt update
apt upgrade -y

# Install VIM
apt install vim -y

# Install PHP 8.2. Load the required config.
apt install software-properties-common -y
add-apt-repository ppa:ondrej/php -y
apt update
apt install php8.2-cgi -y
apt install php8.2 -y
apt install php8.2-xml -y
apt install php8.2-fpm -y
cp /tmp/temp_data/php.ini /etc/php/8.2/cgi/php.ini

apt install unzip -y
apt install curl -y

# Install nginx. Load the required config.
apt install nginx -y
unlink /etc/nginx/sites-enabled/default
cp /tmp/temp_data/default.conf /etc/nginx/conf.d/

# Copy the start script of main container
mkdir -p /etc/main
cp /tmp/temp_data/main_start_script.sh /etc/main/

# Install composer
cd ~
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Install Symphony
curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' |  bash
apt install symfony-cli -y

#mkdir -p /app/exchange-rate-app
#cd /app/exchange-rate-app
#git config --global user.email "jatinnaik.9821@gmail.com"
#git config --global user.name "Jatin"

#cd /app
#symfony new exchange-rate-app --version="6.2.*@dev"

# Remove the non required files/folsers.
apt clean
rm -rf /var/lib/apt/lists/*
rm -rf /tmp/*

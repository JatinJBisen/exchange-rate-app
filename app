#!/usr/bin/sh

apt update
apt upgrade -y

apt install vim -y

apt install software-properties-common -y
add-apt-repository ppa:ondrej/php -y
apt update
apt install php8.2-cgi -y
apt install php8.2 -y
apt install php8.2-xml -y
apt install php8.2-fpm -y

apt install nginx -y
apt install unzip -y
apt install curl -y

mkdir -p /etc/main
cp /tmp/temp_data/main_start_script.sh /etc/main/

unlink /etc/nginx/sites-enabled/default
cp /tmp/temp_data/default.conf /etc/nginx/conf.d/

cd ~
curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php
php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer

curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' |  bash
apt install symfony-cli -y

#mkdir -p /app/exchange-rate-app
#cd /app/exchange-rate-app
#git config --global user.email "jatinnaik.9821@gmail.com"
#git config --global user.name "Jatin"

#cd /app
#symfony new exchange-rate-app --version="6.2.*@dev"

apt clean
rm -rf /var/lib/apt/lists/*
rm -rf /tmp/*

#!/usr/bin/sh

# Temporary commands. Remove this, if the bind volume is changed/removed.
chmod -R 777 /app/exchange-rate-app/var/cache
chmod -R 777 /app/exchange-rate-app/var/log

# Start the services
service php8.2-fpm start
service nginx start

# Run Migrations
cd /app/exchange-rate-app
php bin/console doctrine:migrations:migrate
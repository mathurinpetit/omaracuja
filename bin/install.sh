#!/bin/bash

if [ $# -eq 0 ]
  then
    echo "Usage : bash install.sh unixUserName";
    exit;
fi

echo "INSTALL OMARACUJA"

php composer.phar self-update

php composer.phar install

sudo chmod -R 777 app/cache app/logs

php app/check.php

rm -rf app/cache/* app/logs/*

sudo setfacl -R -m u:www-data:rwx -m u:$1:rwx app/cache app/logs
sudo setfacl -dR -m u:www-data:rwx -m u:$1:rwx app/cache app/logs

php app/console doctrine:schema:drop --force
php app/console doctrine:database:drop --force

php app/console doctrine:database:create
php app/console doctrine:schema:create
php app/console doctrine:fixtures:load

php app/console cache:clear
php app/console assetic:dump
php app/console assets:install

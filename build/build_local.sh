#!/bin/bash
# fileencoding=utf8
# lineends=linux

echo -e "\n### Compiling local release ################################################################################################"

sudo rm -Rf ./release

mkdir -p ../services/storage/temp/
touch ../services/storage/temp/XDEBUG_SESSION.txt.lock

./build.sh

cp -v ./configs/settings.php ./release/config
cp -v ./configs/.htaccess ./release/web
mkdir -vp ./release/storage/temp
chmod -R g+rwx ./release

echo -e "\n### Restaring all services #################################################################################################"
sudo systemctl restart apache2 php7.2-fpm.service

echo -e "\n### Verifying services status ##############################################################################################"
sudo systemctl status apache2 php7.2-fpm.service
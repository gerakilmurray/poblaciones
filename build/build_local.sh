#!/bin/bash
# fileencoding=utf8
# lineends=linux

git submodule update

echo -e "\n### Installing NPM dependencies #############################################################################################"
cd ~/ffg_dev/rcr_repo/frontend
npm install

echo -e "\n### Installing PHP dependencies #############################################################################################"
cd ~/ffg_dev/rcr_repo/services
php composer.phar update
php composer.phar install

echo -e "\n### Copying configuration files #############################################################################################"
cp -v ~/ffg_dev/rcr_repo/build/configs/settings.php ~/ffg_dev/rcr_repo/services/config
cp -v ~/ffg_dev/rcr_repo/build/configs/.htaccess ~/ffg_dev/rcr_repo/services/web
cp -v ~/ffg_dev/rcr_repo/build/configs/dev.env.js ~/ffg_dev/rcr_repo/frontend/config
cp -v ~/ffg_dev/rcr_repo/build/configs/index.js ~/ffg_dev/rcr_repo/frontend/config

echo -e "\n### Cleaning local release #################################################################################################"
cd ~/ffg_dev/rcr_repo/build
sudo rm -Rf ./release

mkdir -p ../services/storage/temp/
touch ../services/storage/temp/XDEBUG_SESSION.txt.lock

echo -e "\n### Installing Git Hooks ###################################################################################################"
cp ./git-hooks/pre-commit ../.git/hooks
chmod +x ../.git/hooks/*

echo -e "\n### Compiling local release ################################################################################################"
./build.sh vendor

echo -e "\n### Removing temp files ####################################################################################################"
rm -vf release.tar.bz2

echo -e "\n### Copying configuration files ############################################################################################"
cp -v ./configs/settings.php ./release/config
cp -v ./configs/.htaccess ./release/web
mkdir -vp ./release/storage/temp
chmod -R g+rwx ./release

echo -e "\n### Restaring all services #################################################################################################"
sudo systemctl restart apache2 php7.2-fpm.service

echo -e "\n### Verifying services status ##############################################################################################"
sudo systemctl status apache2 php7.2-fpm.service --no-pager

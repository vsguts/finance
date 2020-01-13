#!/bin/bash

# Backup
#mkdir -p storage/dumps
#./app mysql/mysqldump storage/dumps/`date +'%Y%m%d_%H%M%S'`.sql

git pull --rebase

# composer self-update
# rm -rf ./vendor
composer install

#./app migrate/up --migrationPath=migrations/dump
./app migrate/up --interactive=0
./app rbac/init

# Cache
./app cache/flush-all
#rm web/css/app.css*
#rm web/css/site.css*

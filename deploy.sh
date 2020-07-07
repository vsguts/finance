#!/bin/bash

# Backup
#mkdir -p storage/dumps
#docker-compose exec php ./app mysql/mysqldump storage/dumps/`date +'%Y%m%d_%H%M%S'`.sql

git pull --rebase

# docker-compose exec php composer self-update
# docker-compose exec php rm -rf ./vendor
docker-compose exec php composer install

#docker-compose exec php ./app migrate/up --migrationPath=migrations/dump
docker-compose exec php ./app migrate/up --interactive=0
docker-compose exec php ./app rbac/init

# Cache
docker-compose exec php ./app cache/flush-all
#docker-compose exec php rm web/css/app.css*
#docker-compose exec php rm web/css/site.css*

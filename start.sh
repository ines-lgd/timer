#!/bin/sh

cp .env.docker .env
composer clear-cache
composer dumpa
composer install
npm install
docker cp . timer:/var/www/project
docker-compose up --build -d
docker exec -it timer bash -c "chmod -R 777 /var/www/project/var/cache/dev && chmod -R 777 /var/www/project/var/log && chmod -R 777 /var/log"
docker cp ./public/index.php timer:/var/www/project/public/index.php

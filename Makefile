down: ##@Docker stop container
	docker-compose down

build: ##@Docker run & install services dependencies
	docker-compose up -d --build

run: ##@Docker run services dependencies
	docker-compose up -d

connect: ##@Docker connect to app container
	docker-compose exec php-fpm bash
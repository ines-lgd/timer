down: ##@Docker stop container
	docker-compose down

build-app: ##@Docker run & install services dependencies
	docker-compose up -d --build

run-app: ##@Docker run services dependencies
	docker-compose up -d


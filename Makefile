.PHONY: backup build deploy preview

DOCKER_IMAGE := sloganator
PORT = "8080"

backup:
	./scripts/backup.sh

build:
	./scripts/build.sh

deploy: backup build
	./scripts/deploy.sh

preview: build
	docker stop sloganator || true
	docker run --rm -it \
		-p $(PORT):80 \
		--name sloganator \
		--user $(shell id -u):$(shell id -g) \
		--volume "$(PWD)/scripts/staging":/var/www/html \
		--volume "$(PWD)/_local/conf":/etc/apache2/sites-enabled \
		$(DOCKER_IMAGE):latest

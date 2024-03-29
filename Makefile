.PHONY: backup build deploy preview

DOCKER_IMAGE := sloganator
PORT = "8080"

clean:
	./scripts/clean.sh

backup:
	./scripts/backup.sh

build: clean
	./scripts/build.sh

deploy: backup build
	./scripts/deploy.sh

preview: clean
	BUILD_MODE=preview ./scripts/build.sh
	mkdir $(PWD)/scripts/staging/mies
	ln -s /var/www/html $(PWD)/scripts/staging/mies/sloganator
	docker stop sloganator || true
	docker run --rm -it \
		-p $(PORT):80 \
		--name sloganator \
		--user $(shell id -u):$(shell id -g) \
		--volume "$(PWD)/scripts/staging":/var/www/html \
		--volume "$(PWD)/_local/conf":/etc/apache2/sites-enabled \
		$(DOCKER_IMAGE):latest

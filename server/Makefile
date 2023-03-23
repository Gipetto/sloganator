.PHONY: local-server install composer-install composer-update test fix-tabs stan clean

DOCKER_IMAGE := sloganator
PORT = "8080"

COMPOSER_COMMAND=
DEV_SERVER_FLAGS=

install: clean docker-build composer-install test

clean:
	rm -rf vendor

composer-install: COMPOSER_COMMAND = install
composer-update: COMPOSER_COMMAND = update
composer-dump-autoload: COMPOSER_COMMAND = dump-autoload
composer-install composer-update composer-dump-autoload:
	docker run --rm -it \
		--name sloganator \
		--volume "$(PWD)":/var/www/html \
		--workdir /var/www/html \
		--user $(shell id -u):$(shell id -g) \
		$(DOCKER_IMAGE):latest \
		composer $(COMPOSER_COMMAND)

docker-build:
	docker build \
		--pull \
		--no-cache \
		--file _local/Dockerfile \
		-t $(DOCKER_IMAGE):latest .
	docker image prune -f

dev-server: DEV_SERVER_FLAGS = --it
dev-server-no-tty: DEV_SERVER_FLAGS = -i --init
dev-server dev-server-no-tty:
	docker run --rm $(DEV_SERVER_FLAGS) \
		-p $(PORT):80 \
		--name sloganator \
		--user $(shell id -u):$(shell id -g) \
		--volume "$(PWD)":/var/www/html \
		--volume "$(PWD)/_local/conf":/etc/apache2/sites-enabled \
		$(DOCKER_IMAGE):latest

stan:
	docker run --rm -it \
		--name sloganator \
		--volume "$(PWD)":/var/www/html \
		--workdir /var/www/html \
		--user $(shell id -u):$(shell id -g) \
		$(DOCKER_IMAGE):latest \
		vendor/bin/phpstan \
			analyse \
			--memory-limit 1G \
			-c phpstan.neon \
			lib index.php

test:
	docker run --rm -it \
		--name sloganator \
		--volume "$(PWD)":/var/www/html \
		--workdir /var/www/html \
		--user $(shell id -u):$(shell id -g) \
		-e XDEBUG_MODE=coverage \
		$(DOCKER_IMAGE):latest \
		vendor/bin/phpunit -v \
			--colors \
			--coverage-clover clover.xml \
			--coverage-html coverage \
			--configuration test/phpunit.xml

# stress:
# 	docker run --rm -it \
# 		--name sloganator \
# 		--volume "$(PWD)":/var/www/html \
# 		--workdir /var/www/html \
# 		--user $(shell id -u):$(shell id -g) \
# 		-e XDEBUG_MODE=profile \
# 		$(DOCKER_IMAGE):latest \
# 		php \
# 		-d xdebug.profiler_enable=On \
# 		-d xdebug.output_dir=. \
# 		trie.php

test-ci:
	vendor/bin/phpunit -v \
		--colors \
		--coverage-clover clover.xml \
		--coverage-html coverage \
		--configuration test/phpunit.xml

stan-ci:
	vendor/bin/phpstan \
		analyse \
		--memory-limit 1G \
		-c phpstan.neon \
		lib index.php

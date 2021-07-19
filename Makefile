.PHONY: local-server test fix-tabs stan

host = "0.0.0.0"
port = "8080"

clean:
	rm -rf vendor

install:
	composer install

local-server:
	@echo "Starting Local Dev Server"
	@echo "Slogans list: http://$(host):$(port)"
	@echo "Sloganator: http://$(host):$(port)/local/index.html"
	php -S \
		$(host):$(port) \
		lib/local/router.php

fix-tabs:
	find lib/ \
		-name "*.php" \
		-type f \
		-exec bash -c 'expand -t 4 "$$0" | sponge "$$0"' {} \;
	find css/ \
		-name "*.css" \
		-type f \
		-exec bash -c 'expand -t 4 "$$0" | sponge "$$0"' {} \;
	find js/ \
		-name "*.js" \
		-type f \
		-exec bash -c 'expand -t 4 "$$0" | sponge "$$0"' {} \;

stan:
	vendor/bin/phpstan \
			analyse \
			--memory-limit 1G \
			-c phpstan.neon \
			lib index.php

test:
	XDEBUG_MODE=coverage vendor/bin/phpunit -v \
		--colors \
		--coverage-clover clover.xml \
		--coverage-html coverage \
		--configuration test/phpunit.xml

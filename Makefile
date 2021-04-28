.PHONY: local-server

host = "0.0.0.0"
port = "8080"

local-server:
	@echo "Starting Local Dev Server"
	@echo "Slogans list: http://$(host):$(port)"
	@echo "Sloganator: http://$(host):$(port)/local/index.html"
	php -S \
		$(host):$(port) \
		lib/local/router.php

stan:
	vendor/bin/phpstan \
			analyse \
			-c phpstan.neon \
			lib index.php


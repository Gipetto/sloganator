.PHONY: local-server

host = "localhost"
port = "8080"

local-server:
	@echo "Starting Local Dev Server"
	@echo "Slogans list: http://$(host):$(port)"
	@echo "Sloganator: http://$(host):$(port)/local/index.html"
	php -S \
		$(host):$(port) \
		lib/local/router.php

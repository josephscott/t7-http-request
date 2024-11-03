SHELL = /bin/bash
.DEFAULT_GOAL := all
HERE := $(dir $(realpath $(firstword $(MAKEFILE_LIST))))

# https://mwop.net/blog/2023-12-11-advent-makefile.html
##@ Help
help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[0-9a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

.PHONY: all
all: style lint analyze tests ## <default> Do everything

# ### #

.PHONY: style
style: ## Fix any style issues
	@echo
	@echo "--> Style: php-cs-fixer"
	vendor/bin/php-cs-fixer fix -v
	@echo

.PHONY: lint
lint: ## Check if the code is valid
	@echo
	@echo "--> Lint"
	php -l src/t7/http/request.php
	php -l src/t7/http/response.php
	@echo

.PHONY: analyze
analyze: ## Static analysis, catch problems in code
	@echo
	@echo "--> Analyze: phpstan"
	vendor/bin/phpstan
	@echo

.PHONY: tests
tests: test-server ## Run tests against local PHP built-in server
	@echo
	@echo "--> Tests: Pest"
	bash -c "./vendor/bin/pest || if pgrep -q -f 'localhost:17171 -t tests/server'; then pkill -f 'localhost:17171 -t tests/server'; fi"
	@echo
	@echo "--> Test Server: stopping"
	@echo
	if pgrep -q -f 'localhost:17171 -t tests/server'; then pkill -f 'localhost:17171 -t tests/server'; fi

.PHONY: test-server
test-server: ## Use built-in PHP server in the background for testing
	@echo
	@echo "--> Test Server: cleaning up"
	@echo
	if pgrep -q -f 'localhost:17171 -t tests/server'; then pkill -f 'localhost:17171 -t tests/server'; fi
	@echo
	@echo "--> Test Server: starting"
	@echo
	{ php -S localhost:17171 -t tests/server/ > phpd.log 2>&1 & echo $$! > /tmp/t7-server-test-server.pid; }
	@echo "Test Server PID saved to /tmp/t7-server-test-server.pid"
	@echo

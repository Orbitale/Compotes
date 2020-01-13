SCRIPT_TITLE_PATTERN := "\033[32m[%s]\033[0m %s\n"

.DEFAULT_GOAL := help
help: ## Show this help
	@printf "\n Available commands:\n\n"
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## */[33m/'
.PHONY: help

##
## Project
## -------
##

start: ## Start the server
	@printf $(SCRIPT_TITLE_PATTERN) "Server" "Start"
	@symfony server:start --daemon
.PHONY: start

stop: ## Stop the web server
	@printf $(SCRIPT_TITLE_PATTERN) "Server" "Stop"
	@symfony server:stop
.PHONY: stop

##
## QA
## --
##

qa: ## Execute QA tools
	$(MAKE) cs
	$(MAKE) phpstan
.PHONY: qa

phpstan: ## Execute PHPStan
	@printf "\n"$(SCRIPT_TITLE_PATTERN) "QA" "phpstan"
	@php vendor/phpstan/phpstan/phpstan analyse
.PHONY: phpstan

cs: ## Execute php-cs-fixer
	@printf $(SCRIPT_TITLE_PATTERN) "QA" "php-cs-fixer"
	@php bin/php-cs-fixer fix
.PHONY: cs

cs-dry: ## Execute php-cs-fixer with a DRY RUN
	@printf $(SCRIPT_TITLE_PATTERN) "QA" "php-cs-fixer"
	@php bin/php-cs-fixer fix --dry-run
.PHONY: cs-dry

lint: ## Execute some linters on the project
	@printf $(SCRIPT_TITLE_PATTERN) "QA" "lint:yaml"
	@php bin/console lint:yaml src config translations

	@printf $(SCRIPT_TITLE_PATTERN) "QA" "lint:container"
	@php bin/console lint:container

	@printf $(SCRIPT_TITLE_PATTERN) "QA" "lint:twig"
	@php bin/console lint:twig --show-deprecations
.PHONY: lint

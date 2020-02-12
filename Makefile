SHELL := /bin/bash

# Config vars
DEFAULT_ADMIN_PASSWORD := admin

CURRENT_DATE = `date "+%Y-%m-%d_%H-%M-%S"`

##
## General purpose
## ---------------
##

.DEFAULT_GOAL := help
help: ## Show this help.
	@printf "\n Available commands:\n\n"
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-25s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## */[33m/'
.PHONY: help

install: vendor node_modules db migrations fixtures test-db admin-password assets start ## Install and start the project.
.PHONY: install

##
## Project
## -------
##

start: start-php start-db ## Start the servers.
.PHONY: start

stop: ## Stop the servers.
	@printf $(SCRIPT_TITLE_PATTERN) "Server" "Stop PHP"
	-@symfony server:stop
	@printf $(SCRIPT_TITLE_PATTERN) "Server" "Stop DB"
	-@docker-compose stop
.PHONY: stop

restart: stop start ## Restart the servers.
.PHONY: restart

cc: ## Clear the cache and warm it up.
	@printf $(SCRIPT_TITLE_PATTERN) "PHP" "Clear cache"
	-@symfony console cache:clear --no-warmup
	@printf $(SCRIPT_TITLE_PATTERN) "PHP" "Warmup cache"
	-@symfony console cache:warmup
.PHONY: cc

vendor: ## Install Composer dependencies.
	@printf ""$(SCRIPT_TITLE_PATTERN) "PHP" "Install Composer dependencies"
	composer install --optimize-autoloader --prefer-dist --no-progress
.PHONY: vendor

node_modules: start-node ## Install Node.js dependencies.
	@printf ""$(SCRIPT_TITLE_PATTERN) "JS" "Install Node.js dependencies"
	@docker-compose exec -T node npm install

assets: start-node ## Build frontend assets.
	@printf ""$(SCRIPT_TITLE_PATTERN) "JS" "Build frontend assets"
	@docker-compose exec -T node npm run-script dev
.PHONY: assets

assets-watch: start-node ## Watch assets to rebuild them on change (runs in the foreground)
	@printf ""$(SCRIPT_TITLE_PATTERN) "JS" "Watch assets to rebuild them on change"
	@docker-compose exec node npm run-script watch
.PHONY: assets-watch

db: start-db wait-for-db ## Create a database for the project
	@printf ""$(SCRIPT_TITLE_PATTERN) "DB" "Drop existing database"
	@symfony console doctrine:database:drop --no-interaction --if-exists --force
	@printf ""$(SCRIPT_TITLE_PATTERN) "DB" "Create database"
	@symfony console doctrine:database:create --no-interaction --if-not-exists
.PHONY: db-container

start-db:
	@printf $(SCRIPT_TITLE_PATTERN) "Server" "Start DB"
	@docker-compose up --detach database
.PHONY: start-db

wait-for-db:
	@set -xe \
	&& printf "\n"$(SCRIPT_TITLE_PATTERN) "DB" "Waiting for database..." \
	&& bin/wait-for-db.bash
.PHONY: wait-for-db

migrations: wait-for-db ## Create database schema through migrations.
	@printf ""$(SCRIPT_TITLE_PATTERN) "DB" "Run migrations"
	@symfony console doctrine:migrations:migrate --no-interaction
.PHONY: migrations

fixtures: wait-for-db ## Add default data to the project.
	@printf ""$(SCRIPT_TITLE_PATTERN) "DB" "Install fixture data in the database"
	@symfony console doctrine:fixtures:load --no-interaction --append
	@symfony console operations:import --no-interaction
	@symfony console operations:update-tags --no-interaction
.PHONY: fixtures

start-node:
	@docker-compose up --detach node
.PHONY: start-node

start-php:
	@printf $(SCRIPT_TITLE_PATTERN) "Server" "Start PHP"
	-@symfony server:stop >/dev/null 2>&1
	-@symfony server:start --daemon
.PHONY: start-php

admin-password: ## Reset the admin password. Use "-e DEFAULT_ADMIN_PASSWORD=your-password" to use something else than "admin"
	@touch .env.local

	@export password=$$( \
		symfony console security:encode-password ${DEFAULT_ADMIN_PASSWORD} 2>/dev/null \
			| grep "Encoded password" \
			| sed -e 's/Encoded password//' -e 's/^[ \t]*//' -e 's/^[ \t]*//' \
		); \
	if grep -e "ADMIN_PASSWORD=" ".env.local" >/dev/null ; then \
		printf $(SCRIPT_TITLE_PATTERN) "PHP" 'Overwrite existing password in ".env.local"'; \
		echo $$password > .env.local.bak; \
		export password=$$(cat .env.local.bak | sed -e 's/\$$/\\\$$/g' -e 's~/~\\\\/~g' -e 's/\+/\\\+/g'); \
		rm .env.local.bak; \
		sed -i "s/ADMIN_PASSWORD\=.*/ADMIN_PASSWORD\='$$password'/g" .env.local; \
	else \
		printf $(SCRIPT_TITLE_PATTERN) "PHP" 'Add password to ".env.local"'; \
		echo "ADMIN_PASSWORD='"$$password"'" >> .env.local; \
	fi
.PHONY: admin-password

dump: ## Dump the current database to keep a track of it.
	@printf $(SCRIPT_TITLE_PATTERN) "DB" "Dump current database to var/dump_$(CURRENT_DATE).sql"; \
	docker-compose exec -T database mysqldump -uroot -proot main > var/dump_$(CURRENT_DATE).sql
.PHONY: dump

##
## QA
## --
##

test-db: start-db wait-for-db ## Sets up the test database
	@printf ""$(SCRIPT_TITLE_PATTERN) "Test DB" "Drop existing database"
	@APP_ENV=test php bin/console doctrine:database:drop --no-interaction --if-exists --force
	@printf ""$(SCRIPT_TITLE_PATTERN) "Test DB" "Create database"
	@APP_ENV=test php bin/console doctrine:database:create --no-interaction
	@APP_ENV=test php bin/console doctrine:schema:create --no-interaction
	@printf ""$(SCRIPT_TITLE_PATTERN) "Test DB" "Install fixture data in the database"
	@APP_ENV=test php bin/console doctrine:fixtures:load --no-interaction --append
	@APP_ENV=test php bin/console operations:import --no-interaction
	@APP_ENV=test php bin/console operations:update-tags --no-interaction
.PHONY: test-db

install-phpunit:
	@APP_ENV=test symfony php bin/phpunit --version
.PHONY: install-phpunit

phpunit: ## Execute the PHPUnit test suite
	@APP_ENV=test symfony php bin/phpunit
.PHONY: phpunit

qa: ## Execute QA tools
	$(MAKE) security-check
	$(MAKE) cs
	$(MAKE) phpstan
.PHONY: qa

security-check: ## Execute the Symfony Security checker
	@symfony security:check
.PHONY: security-check

phpstan: ## Execute PHPStan
	@printf "\n"$(SCRIPT_TITLE_PATTERN) "QA" "phpstan"
	@symfony php vendor/phpstan/phpstan/phpstan analyse
.PHONY: phpstan

cs: ## Execute php-cs-fixer
	@printf $(SCRIPT_TITLE_PATTERN) "QA" "php-cs-fixer"
	@symfony php bin/php-cs-fixer fix
.PHONY: cs

cs-dry: ## Execute php-cs-fixer with a DRY RUN
	@printf $(SCRIPT_TITLE_PATTERN) "QA" "php-cs-fixer"
	@symfony php bin/php-cs-fixer fix --dry-run
.PHONY: cs-dry

lint: ## Execute some linters on the project
	@printf $(SCRIPT_TITLE_PATTERN) "QA" "lint:yaml"
	@symfony console lint:yaml src config translations

	@printf $(SCRIPT_TITLE_PATTERN) "QA" "lint:container"
	@symfony console lint:container

	@printf $(SCRIPT_TITLE_PATTERN) "QA" "lint:twig"
	@symfony console lint:twig --show-deprecations
.PHONY: lint

# Helper vars
SCRIPT_TITLE_PATTERN := "\033[32m[%s]\033[0m %s\n"
SCRIPT_ERROR_PATTERN := "\033[31m[%s]\033[0m %s\n"

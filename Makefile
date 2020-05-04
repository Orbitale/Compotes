SHELL := /bin/bash

# Config vars
PASSWORD := admin

CURRENT_DATE = `date "+%Y-%m-%d_%H-%M-%S"`

##
## General purpose commands
## ------------------------
##

.DEFAULT_GOAL := help
help: ## Show this help.
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-25s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m## */[33m/'
.PHONY: help

install: vendor node_modules db migrations fixtures test-db admin-password assets start ## Install and start the project.
.PHONY: install

##
## Development
## -----------
##

start: start-php start-db ## Start the servers.
.PHONY: start

stop: ## Stop the servers. [1][2]
	@printf $(_TITLE) "Server" "Stop PHP"
	-@symfony server:stop
	@printf $(_TITLE) "Server" "Stop DB"
	-@docker-compose stop
.PHONY: stop

restart: stop start ## Restart the servers.
.PHONY: restart

open: ## Open the web browser at the homepage [1]
	@symfony open:local
.PHONY: open

cc: ## Clear the cache and warm it up.
	@printf $(_TITLE) "PHP" "Clear cache"
	-@php bin/console cache:clear --no-warmup
	@printf $(_TITLE) "PHP" "Warmup cache"
	-@php bin/console cache:warmup
.PHONY: cc

vendor: ## Install Composer dependencies.
	@printf ""$(_TITLE) "PHP" "Install Composer dependencies"
	composer install --optimize-autoloader --prefer-dist --no-progress
.PHONY: vendor

node_modules: start-node ## Install Node.js dependencies. [2]
	@printf ""$(_TITLE) "JS" "Install Node.js dependencies"
	@docker-compose exec -T node npm install

assets: start-node ## Build frontend assets. [2]
	@printf ""$(_TITLE) "JS" "Build frontend assets"
	@docker-compose exec -T node npm run-script dev
.PHONY: assets

assets-watch: start-node ## Watch assets to rebuild them on change (runs in the foreground). [2]
	@printf ""$(_TITLE) "JS" "Watch assets to rebuild them on change"
	@docker-compose exec node npm run-script watch
.PHONY: assets-watch

db: start-db wait-for-db ## Create a database for the project
	@printf ""$(_TITLE) "DB" "Drop existing database"
	@php bin/console doctrine:database:drop --no-interaction --if-exists --force
	@printf ""$(_TITLE) "DB" "Create database"
	@php bin/console doctrine:database:create --no-interaction --if-not-exists
.PHONY: db-container

start-db: ## Start the database server. [2]
	@printf $(_TITLE) "Server" "Start DB"
	@docker-compose up --detach database
.PHONY: start-db

wait-for-db:
	@set -xe \
	&& printf "\n"$(_TITLE) "DB" "Waiting for database..." \
	&& bin/wait-for-db.bash
.PHONY: wait-for-db

migrations: wait-for-db ## Create database schema through migrations.
	@printf ""$(_TITLE) "DB" "Run migrations"
	@php bin/console doctrine:migrations:migrate --no-interaction
.PHONY: migrations

fixtures: wait-for-db ## Add default data to the project.
	@printf ""$(_TITLE) "DB" "Install fixture data in the database"
	@php bin/console doctrine:fixtures:load --no-interaction --append
	@php bin/console operations:update-tags --no-interaction
.PHONY: fixtures

start-node:
	@docker-compose up --detach node
.PHONY: start-node

start-php:
	@printf $(_TITLE) "Server" "Start PHP"
	-@symfony server:stop >/dev/null 2>&1
	-@symfony server:start --daemon
.PHONY: start-php

admin-password: ## Reset the admin password in ".env.local". Use "make -e PASSWORD=your-password admin-password" to use something else than "admin".
	@touch .env.local

	@export password=$$(php bin/generate_password.php "${PASSWORD}"); \
	if grep -e "ADMIN_PASSWORD=" ".env.local" >/dev/null ; then \
		printf $(_TITLE) "PHP" 'Overwrite existing password in ".env.local"'; \
		echo $$password > .env.local.bak; \
		export password=$$(cat .env.local.bak | sed -e 's/\$$/\\\$$/g' -e 's~/~\\\\/~g' -e 's/\+/\\\+/g'); \
		rm .env.local.bak; \
		sed -i "s/ADMIN_PASSWORD\=.*/ADMIN_PASSWORD\='$$password'/g" .env.local; \
	else \
		printf $(_TITLE) "PHP" 'Add password to ".env.local"'; \
		echo "ADMIN_PASSWORD='"$$password"'" >> .env.local; \
	fi
.PHONY: admin-password

dump: ## Dump the current database to keep a track of it.
	@printf $(_TITLE) "DB" "Dump current database to var/dump_$(CURRENT_DATE).sql"; \
	docker-compose exec -T database mysqldump -uroot -proot main > var/dump_$(CURRENT_DATE).sql
.PHONY: dump

##
## Quality assurance
## -----------------
##

test-db: start-db wait-for-db ## Set up the test database
	@printf ""$(_TITLE) "Test DB" "Drop existing database"
	@APP_ENV=test php bin/console doctrine:database:drop --no-interaction --if-exists --force
	@printf ""$(_TITLE) "Test DB" "Create database"
	@APP_ENV=test php bin/console doctrine:database:create --no-interaction
	@APP_ENV=test php bin/console doctrine:schema:create --no-interaction
	@printf ""$(_TITLE) "Test DB" "Install fixture data in the database"
	@APP_ENV=test php bin/console doctrine:fixtures:load --no-interaction --append
	@APP_ENV=test php bin/console operations:update-tags --no-interaction
.PHONY: test-db

install-phpunit:
	@APP_ENV=test php bin/phpunit --version
.PHONY: install-phpunit

phpunit: ## Execute the PHPUnit test suite.
	@APP_ENV=test php bin/phpunit
.PHONY: phpunit

qa: ## Execute QA tools.
	$(MAKE) security-check
	$(MAKE) cs
	$(MAKE) phpstan
.PHONY: qa

security-check: ## Execute the Symfony Security checker. [1]
	@symfony security:check
.PHONY: security-check

phpstan: ## Execute PHPStan.
	@printf "\n"$(_TITLE) "QA" "phpstan"
	@php vendor/phpstan/phpstan/phpstan analyse
.PHONY: phpstan

cs: ## Execute php-cs-fixer.
	@printf $(_TITLE) "QA" "php-cs-fixer"
	@php bin/php-cs-fixer fix
.PHONY: cs

cs-dry: ## Execute php-cs-fixer with a DRY RUN.
	@printf $(_TITLE) "QA" "php-cs-fixer"
	@php bin/php-cs-fixer fix --dry-run
.PHONY: cs-dry

lint: ## Execute some linters on the project.
	@printf $(_TITLE) "QA" "lint:yaml"
	@php bin/console lint:yaml src config translations

	@printf $(_TITLE) "QA" "lint:container"
	@php bin/console lint:container

	@printf $(_TITLE) "QA" "lint:twig"
	@php bin/console lint:twig --show-deprecations
.PHONY: lint

##
## Deployment
## ----------
##

setup-heroku: ## Set the project up to use Heroku.
	bash heroku/create_project

	@printf $(_TITLE) "Heroku" "Heroku project is set and pushed! If you want to entirely delete your application later, you can use the \"heroku apps:destroy\" command."
.PHONY: setup-heroku

setup-prod: ## Set the project up when installed on a server (dedicated, VPS, etc.).
	@printf $(_TITLE) "Deploy" "Installing PHP dependencies (necessary for Symfony Flex)..."
	-@APP_ENV=prod composer install --no-interaction --prefer-dist --no-dev --no-scripts

	@printf $(_TITLE) "Deploy" "Please enter the password for the administration panel" \
	&& echo -n " > " \
	&& read -r PASSWORD \
	&& APP_ENV=prod \
	 APP_SECRET=$(php bin/generate_password.php "${PASSWORD}") \
	 ADMIN_PASSWORD=$(php bin/generate_secret.php") \
	 composer symfony:dump-env prod

	@echo ""
	@printf $(_INFO) "Almost finished!"
	@printf " You now need to check your \033[32m\".env.local.php\"\033[0m for the \033[32m'DATABASE_URL'\033[0m field and update its values to your own database credentials.\n"
.PHONY: setup-prod

deploy: ## Common script after pushing the changes on the server.
	bin/deploy.bash
.PHONY: deploy

# Helper vars
_TITLE := "\033[32m[%s]\033[0m %s\n"
_INFO := "\033[32m %s\033[0m\n"
_ERROR := "\033[31m[%s]\033[0m %s\n"

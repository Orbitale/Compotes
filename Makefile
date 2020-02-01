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

install: vendor node_modules db migrations fixtures admin-password assets start ## Install and start the project.
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

node_modules: ## Install Node.js dependencies.
	@printf ""$(SCRIPT_TITLE_PATTERN) "JS" "Install Node.js dependencies"
	@npm install

assets: ## Build frontend assets.
	@printf ""$(SCRIPT_TITLE_PATTERN) "JS" "Build frontend assets"
	@npm run-script dev
.PHONY: assets

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

MAX_DB_HEALTH_ATTEMPTS := 15
wait-for-db:
	@printf "\n"$(SCRIPT_TITLE_PATTERN) "DB" "Waiting for database..."
	@for i in {1..${MAX_DB_HEALTH_ATTEMPTS}}; do \
		docker-compose exec database mysql -uroot -proot -e "SELECT 1;" >/dev/null 2>&1; \
		if [[ $$? == 0 ]]; then \
			printf ""$(SCRIPT_TITLE_PATTERN) "DB" "Ok!"; \
			exit 0; \
		elif [[ $$i == ${MAX_DB_HEALTH_ATTEMPTS} ]]; then \
			printf ""$(SCRIPT_ERROR_PATTERN) "ERR" "Cannot connect to mysql..." ;\
			exit 1; \
		fi; \
		echo -e ".\c";\
		sleep 1; \
	done
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

# Helper vars
SCRIPT_TITLE_PATTERN := "\033[32m[%s]\033[0m %s\n"
SCRIPT_ERROR_PATTERN := "\033[31m[%s]\033[0m %s\n"

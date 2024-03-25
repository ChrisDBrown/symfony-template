# Executables (local)
DOCKER_COMP = docker compose

# Docker containers
PHP_CONT = $(DOCKER_COMP) exec php

# Executables
PHP      = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY  = $(PHP) bin/console

# Misc
.DEFAULT_GOAL = help
.PHONY        : help build up start down logs sh composer vendor sf cc test

## —— 🎵 🐳 The Symfony Docker Makefile 🐳 🎵 ——————————————————————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Docker 🐳 ————————————————————————————————————————————————————————————————
build: ## Builds the Docker images
	@$(DOCKER_COMP) build --pull --no-cache

up: ## Start the docker hub in detached mode (no logs)
	@$(DOCKER_COMP) up --detach

start: build up ## Build and start the containers

down: ## Stop the docker hub
	@$(DOCKER_COMP) down --remove-orphans

logs: ## Show live logs
	@$(DOCKER_COMP) logs --tail=0 --follow

sh: ## Connect to the FrankenPHP container
	@$(PHP_CONT) sh

## —— Tools 🛠️ ——————————————————————————————————————————————————————————————————
test: ## Start tests with phpunit, pass the parameter "c=" to add options to phpunit, example: make test c="--group e2e --stop-on-failure"
	@$(eval c ?=)
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit $(c)

test-functional: ## Run functional testsuite with phpunit
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit --testsuite Functional

test-integration: ## Run integration testsuite with phpunit
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit --testsuite Integration

test-unit: ## Run unit testsuite with phpunit
	@$(DOCKER_COMP) exec -e APP_ENV=test php bin/phpunit --testsuite Unit

stan: ## Run PHPStan analyse
	@$(PHP_CONT) vendor/bin/phpstan analyse

rector: ## Run Rector analysis with --dry-run flag to report errors without fixing
	@$(PHP_CONT) vendor/bin/rector --dry-run

rector-fix: ## Run Rector analysis and auto-fix issues
	@$(PHP_CONT) vendor/bin/rector

cs: ## Run coding standards check with --dry-run flag to report errors without fixing
	@$(PHP_CONT) vendor/bin/php-cs-fixer fix --dry-run --allow-risky=yes -v

cs-fix: ## Run coding standards check and auto-fix issues
	@$(PHP_CONT) vendor/bin/php-cs-fixer fix --allow-risky=yes

deptrac: ## Check our domain boundaries haven't been crossed
	@$(PHP_CONT) vendor/bin/deptrac

db-check:
	@$(SYMFONY) doctrine:schema:validate

var-dump:
	@$(PHP_CONT) vendor/bin/var-dump-check --symfony --exclude vendor .

all: test stan rector cs deptrac var-dump ## Run all our code quality tools as one

## —— Database 📋 ——————————————————————————————————————————————————————————————
db-reset:
	@$(SYMFONY) doctrine:database:drop --force --if-exists
	@$(SYMFONY) doctrine:database:drop --force --if-exists --env=test
	@$(SYMFONY) doctrine:database:create --if-not-exists
	@$(SYMFONY) doctrine:database:create --if-not-exists --env=test

migrations-diff: ##@Doctrine Generates a database migration by comparing the current database to the mapping information
	@$(SYMFONY) doctrine:migrations:diff
	@$(PHP_CONT) vendor/bin/php-cs-fixer fix src/Data/Migrations

migrations-create: ##@Doctrine Generates a blank database migration
	@$(SYMFONY) doctrine:migrations:generate
	@$(PHP_CONT) vendor/bin/php-cs-fixer fix src/Data/Migrations

migrations-migrate: ##@Doctrine Runs the database migrations
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration
	@$(SYMFONY) doctrine:migrations:migrate --no-interaction --allow-no-migration --env=test

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction
vendor: composer

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: ## Clear the cache
	@$(SYMFONY) c:c
	@$(SYMFONY) c:c --env=test

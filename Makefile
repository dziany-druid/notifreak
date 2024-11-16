DOCKER_COMP = docker compose
PHP_CONT = $(DOCKER_COMP) exec php
PHP = $(PHP_CONT) php
COMPOSER = $(PHP_CONT) composer
SYMFONY = $(PHP) bin/console

.DEFAULT_GOAL = help
.PHONY: help build up start down logs sh composer vendor sf cc test

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

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

## —— Symfony 🎵 ———————————————————————————————————————————————————————————————
sf: ## List all Symfony commands or pass the parameter "c=" to run a given command, example: make sf c=about
	@$(eval c ?=)
	@$(SYMFONY) $(c)

cc: c=c:c ## Clear the cache
cc: sf

## —— Tools 🛠 ———————————————————————————————————————————————————————————————
grumphp: ## Run GrumPHP
	@$(DOCKER_COMP) run --rm --no-deps php composer grumphp

php-cs-fixer: ## Fix code formatting with PHP Coding Standards Fixer
	@$(DOCKER_COMP) run --rm --no-deps php composer php-cs-fixer

phpstan: ## Analyse code with PHPStan
	@$(DOCKER_COMP) run --rm --no-deps php composer phpstan

phpunit: ## Start tests with PHPUnit
	@$(DOCKER_COMP) run --rm --no-deps php composer phpunit

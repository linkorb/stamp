.PHONY: help

.PHONY: clean
clean: ## Clean working directory
	@echo "Cleaning"
	@rm -rf ./vendor
	@rm -f .env
	@echo "Done!"

.env:
	@echo "Copying .env from .env.dist"
	@cp .env.dist .env

build: .env vendor ## Build it
	@echo "Building"

composer.lock: ## Generate composer.lock
	@echo "Generating composer.lock"
	@composer update

vendor: composer.lock
	@echo "Installing vendor libraries ..."
	@composer install
	@touch vendor/

test: build phpqa-phpcs

phpqa-phpcs:
	vendor/bin/phpcs --standard=PSR2 src/

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

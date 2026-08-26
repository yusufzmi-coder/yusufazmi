# Semua arahan PHP berjalan dalam container — mesin host tiada PHP/Composer.
SHELL := /bin/bash
DC    := docker compose
EXEC  := $(DC) exec -T app

.PHONY: help up down build sh art test fresh composer npm logs pint stan

help:
	@grep -E '^[a-z-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN{FS=":.*?## "}{printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'

up: ## Naikkan semua container
	$(DC) up -d --build

down: ## Turunkan semua container
	$(DC) down

build: ## Bina semula imej app
	$(DC) build --no-cache app

sh: ## Masuk shell container app
	$(DC) exec app sh

art: ## make art c="migrate:fresh --seed"
	$(EXEC) php artisan $(c)

composer: ## make composer c="require spatie/laravel-permission"
	$(EXEC) composer $(c)

npm: ## make npm c="run build"
	$(DC) run --rm -T vite npm $(c)

test: ## Jalankan Pest
	$(EXEC) php artisan test

fresh: ## Migrasi semula + seed
	$(EXEC) php artisan migrate:fresh --seed

pint: ## Format kod PHP
	$(EXEC) ./vendor/bin/pint

stan: ## Analisis statik
	$(EXEC) ./vendor/bin/phpstan analyse --memory-limit=1G

logs: ## Ikuti log container
	$(DC) logs -f

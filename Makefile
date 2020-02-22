USER              := $(shell id -u)
GROUP             := $(shell id -g)
.DEFAULT_GOAL     := help
EXEC_PHP          := ./bin/
PHPDOCUMENTORURL  := https://github.com/phpDocumentor/phpDocumentor2/releases/download/v2.9.0/phpDocumentor.phar
PHPDOCUMENTORFILE := phpDocumentor.phar
PHPFPM            := labstag_phpfpm
MARIADB           := labstag_mariadb
APACHE            := labstag_apache
STACK             := labstag
PHPFPMFULLNAME    := $(PHPFPM).1.$$(docker service ps -f 'name=$(PHPFPM)' $(PHPFPM) -q --no-trunc | head -n1)
MARIADBFULLNAME   := $(MARIADB).1.$$(docker service ps -f 'name=$(MARIADB)' $(MARIADB) -q --no-trunc | head -n1)
APACHEFULLNAME    := $(APACHE).1.$$(docker service ps -f 'name=$(APACHE)' $(APACHE) -q --no-trunc | head -n1)
ARGS              := $(filter-out $@,$(MAKECMDGOALS))
	
.PHONY: help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
	
.PHONY: commit
commit: ## Commit data
	@npm run commit

.PHONY: cache-clear
cache-clear: ## Cache clear
	docker exec $(PHPFPMFULLNAME) php bin/console c:c

.PHONY: deploy
deploy: ## deploy
	docker stack deploy -c docker-compose.yml $(STACK)

.PHONY: update
update: ## update DEPEDENCIES
	npm update
	@make composer-update -i
	@make npm-update -i

.PHONY: pull
pull: ## Update repository
	npm install
	@make composer-install-dev -i
	@make npm-install-dev -i

.PHONY: install-dev
install-dev: ## install DEV
	npm install
	@make deploy -i
	@make composer-install-dev -i
	@make npm-install-dev -i
	@make bdd-dev -i
	@make migrate -i
	@make fixtures -i
	docker exec $(PHPFPMFULLNAME) npm run dev


.PHONY: npm-doctor
npm-doctor: ## doctor NPM
	docker exec $(PHPFPMFULLNAME) npm doctor

.PHONY: npm-clean-install
npm-clean-install: ## install PROD
	docker exec $(PHPFPMFULLNAME) npm clean-install

.PHONY: npm-install-dev
npm-install-dev: ## npm install PROD
	docker exec $(PHPFPMFULLNAME) npm install

.PHONY: npm-install-prod
npm-install-prod: ## npm install PROD
	docker exec $(PHPFPMFULLNAME) npm install

.PHONY: npm-update
npm-update: ## npm update PROD
	docker exec $(PHPFPMFULLNAME) npm update

.PHONY: install-prod
install-prod: ## install PROD
	npm install
	@make deploy -i
	@make composer-install-prod -i
	@make npm-install-prod -i
	@make bdd-dev -i
	@make migrate -i
	docker exec $(PHPFPMFULLNAME) npm run build

.PHONY: migrate
migrate: ## migrate database
	docker exec $(PHPFPMFULLNAME) php bin/console doctrine:migrations:migrate -n

.PHONY: logs
logs: ## logs docker
	docker-compose logs -f

.PHONY: logs-mariadb
logs-mariadb: ## logs docker mariadb
	docker-compose logs -f labstag-mariadb

.PHONY: composer-install-dev
composer-install-dev: ## COMPOSER install DEV
	docker exec $(PHPFPMFULLNAME) composer install

.PHONY: composer-install-prod
composer-install-prod: ## COMPOSER install PROD
	docker exec $(PHPFPMFULLNAME) composer install --no-dev

.PHONY: composer-update
composer-update: ## COMPOSER update
	docker exec $(PHPFPMFULLNAME) composer update

.PHONY: composer-validate
composer-validate: ## COMPOSER validate
	docker exec $(PHPFPMFULLNAME) composer validate

.PHONY: ssh
ssh: ## ssh
	docker exec -ti $(PHPFPMFULLNAME) /bin/bash

.PHONY: stop
stop: ## Stop docker
	docker stack rm $(STACK)

.PHONY: docker-recreate
docker-recreate: ## RECREATE docker
	@make docker-stop -i
	@make docker-start -i
	
.PHONY: licenses
licenses: ## Show licenses
	@make licensesPHP -i
	@make licensesJSCSS -i
	
.PHONY: licensesPHP
licensesPHP: ## Show licenses PHP
	docker exec $(PHPFPMFULLNAME) composer licenses
	
.PHONY: licensesJSCSS
licensesJSCSS: ## Show licenses JS / CSS
	@npm run licenses
	
.PHONY: phpdoc
phpdoc: phpdoc.dist.xml ## PHPDoc
	@rm -rf public/docs/api 
	@rm -rf output
	@wget -nc $(PHPDOCUMENTORURL)
	@php phpDocumentor.phar
	@rm -rf output
	
.PHONY: watch-localhost
watch-localhost: ## WEBPACK DEV
	@export NODE_ENV=localhost && npm run watch
	
.PHONY: phpcsfixer
phpcsfixer: ## PHPCSFIXER
	docker exec $(PHPFPMFULLNAME) composer php-cs-fixer
	
.PHONY: phpcbf
phpcbf: ## PHPCBF
	docker exec $(PHPFPMFULLNAME) composer phpcbf
	
.PHONY: phpmd
phpmd: ## PHPMD
	docker exec $(PHPFPMFULLNAME) composer phpmd
	
.PHONY: phpcs
phpcs: ## PHPCS
	docker exec $(PHPFPMFULLNAME) composer phpcs
	
.PHONY: phpstan
phpstan: ## PHPSTAN
	docker exec $(PHPFPMFULLNAME) composer phpstan
	
.PHONY: phpcpd
phpcpd: ## PHPCPD
	docker exec $(PHPFPMFULLNAME) composer phpcpd
	
.PHONY: phpmnd
phpmnd: ## PHPMND
	docker exec $(PHPFPMFULLNAME) composer phpmnd
	
.PHONY: twigcs
twigcs: ## TWIGCS
	docker exec $(PHPFPMFULLNAME) composer twigcs

.PHONY: fix
fix: ## FIX CODE PHP
	@make phpcsfixer -i
	@make phpcbf -i

.PHONY: fixaudit
fixaudit: ## Fix and audit file
	@make fix -i
	@make audit -i

.PHONY: audit
audit: ## AUDIT CODE PHP
	@make phpmd -i
	@make phpcs -i
	@make phpstan -i
	@make phpcpd -i
	@make phpmnd -i
	@make twigcs -i

.PHONY: fixtures
fixtures: ## PHPUnit
	docker exec $(PHPFPMFULLNAME) php bin/console doctrine:fixtures:load -n

.PHONY: tests
tests: ## tests
	@make phpunit -i

.PHONY: phpunit
phpunit: ## PHPUnit
	docker exec $(PHPFPMFULLNAME) composer phpunit

.PHONY: bdd-dev
bdd-dev: ## Install BDD DEV
	docker exec $(PHPFPMFULLNAME) cp .env.dist .env

.PHONY: watch
watch: ## watch JS / CSS DEV
	docker exec $(PHPFPMFULLNAME) npm run watch

.PHONY: create-asset
create-asset: ## create ASSET
	docker exec $(PHPFPMFULLNAME) npm run dev

.PHONY: git-author
git-author: ## git author
	@git log --pretty=%an\ \<%ae\>|sort|uniq -c

.PHONY: git-check
git-check: ## CHECK git
	@git gc
	@git prune
	@git fetch

# Makefile for Docker Nginx PHP Composer MySQL

include .env

# MySQL
MYSQL_DUMPS_DIR=data/db/dumps

help:
	@echo ""
	@echo "usage: make COMMAND"
	@echo ""
	@echo "Commands:"
	@echo "  composer-install    Install PHP dependencies with composer"
	@echo "  composer-update     Update PHP dependencies with composer"
	@echo "  start               Create and start containers"
	@echo "  stop                Stop and clear all services"
	@echo "  mysql-dump          Create backup of whole database"
	@echo "  mysql-restore       Restore backup from whole database"

up:
	docker-compose up -d

stop:
	docker-compose down -v

logs:
	@docker-compose logs -f

mysql-dump:
	@mkdir -p $(MYSQL_DUMPS_DIR)
	@docker exec $(shell docker-compose ps -q mysql) mysqldump -u"dev" -p"dev" typo3 > $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null

mysql-restore:
	@docker exec -i $(shell docker-compose ps -q mysql) mysql -u"dev" -p"dev" typo3 < $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null

composer-update:
	@docker exec -i $$(shell docker-compose ps -q app) composer update -d /app/

composer-install:
	@docker exec -i $$(shell docker-compose ps -q app) composer install -d /app/

tests-functional:
	docker exec -i $(shell docker-compose ps -q app) /app/.Build/vendor/phpunit/phpunit/phpunit --configuration /app/Tests/Build/FunctionalTests.xml

rebuild:
	docker-compose stop
	docker-compose pull
	docker-compose rm --force app
	docker-compose build --no-cache --pull
	docker-compose up -d --force-recreate

bash: shell

shell:
	docker-compose exec --user application app /bin/bash

root:
	docker-compose exec --user root app /bin/bash

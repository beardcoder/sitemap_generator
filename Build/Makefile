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
	@echo "  docker-start        Create and start containers"
	@echo "  docker-stop         Stop and clear all services"
	@echo "  mysql-dump          Create backup of whole database"
	@echo "  mysql-restore       Restore backup from whole database"

docker-start:
	docker-compose up -d

docker-stop:
	docker-compose down -v

logs:
	@docker-compose logs -f

mysql-dump:
	@mkdir -p $(MYSQL_DUMPS_DIR)
	@docker exec $(shell docker-compose ps -q mysqldb) mysqldump -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" typo3 > $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null

mysql-restore:
	@docker exec -i $(shell docker-compose ps -q mysqldb) mysql -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" typo3 < $(MYSQL_DUMPS_DIR)/db.sql 2>/dev/null

composer-update:
	@docker exec -i $(shell docker-compose ps -q php) composer update -d /var/www/html/

composer-install:
	@docker exec -i $(shell docker-compose ps -q php) composer install -d /var/www/html/

bash: shell

shell:
	docker-compose exec --user application php /bin/bash
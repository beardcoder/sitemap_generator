ARGS = $(filter-out $@,$(MAKECMDGOALS))
MAKEFLAGS += --silent

include .env

list:
	make -rpn | sed -n -e '/^$$/ { n ; /^[^ .#][^ ]*:/ { s/:.*$$// ; p ; } ; }'

up:
	docker-compose up -d

stop:
	docker-compose stop

down:
	docker-compose down -v

logs:
	@docker-compose logs -f

mysql-dump:
	@mkdir -p $(MYSQL_DUMPS_DIR)
	@docker exec $(shell docker-compose ps -q mysqldb) mysqldump -u"$(MYSQL_ROOT_USER)" -p"$(MYSQL_ROOT_PASSWORD)" typo3 > /data/db/dumps/db.sql

mysql-restore:
	@docker exec $(shell docker-compose ps -q mysql) mysql -u"dev" -p"dev" typo3 < ./data/db/dumps/db.sql

composer-update:
	@docker exec -i $(shell docker-compose ps -q php) composer update -d /app/

bash: shell

shell:
	docker-compose exec --user application php /bin/bash

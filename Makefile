include .env


APP_NAME = MySQL in docker compose

SHELL ?= /bin/bash
ARGS = $(filter-out $@,$(MAKECMDGOALS))
DIR = $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST))))
DIR2 = $(notdir $(shell pwd)) ## just in case for history

.SILENT: ;               # no need for @
.ONESHELL: ;             # recipes execute in same shell
.NOTPARALLEL: ;          # wait for this target to finish
.EXPORT_ALL_VARIABLES: ; # send all vars to shell
Makefile: ;              # skip prerequisite discovery

# Run make help by default
.DEFAULT_GOAL = help

ifneq ("$(wildcard ./VERSION)","")
VERSION ?= $(shell cat ./VERSION | head -n 1)
else
VERSION ?= 8.0
endif

.env:
	cp $@.dist $@

up: ## Starts and attaches to containers for a service
	docker compose up -d

down: ## Down all containers.
	docker compose down

start: ## Start containers.
	docker compose start

stop: ## Stop containers.
	docker compose stop

reset: down up

bash: ## Go to the application container.
	docker compose exec php sh

mysql: ## Start MySQL client.
	docker compose exec mysql mysql -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(ARGS)

mysqldump: ## MySQL Dump tool
	docker compose exec mysql mysqldump -u$(MYSQL_USER) -p$(MYSQL_PASSWORD) $(ARGS)

help: ## Show this help and exit
	$(info $(APP_NAME) v$(VERSION))
	echo ''
	printf "                   %s: \033[94m%s\033[0m \033[90m[%s] [%s]\033[0m\n" "Usage" "make" "target" "ENV_VARIABLE=ENV_VALUE ..."
	echo ''
	echo '                   Available targets:'
	# Print all commands, which have '##' comments right of it's name.
	# Commands gives from all Makefiles included in project.
	# Sorted in alphabetical order.
	echo '                   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
	grep -hE '^[a-zA-Z. 0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | \
		 awk 'BEGIN {FS = ":.*?## " }; {printf "\033[36m%+18s\033[0m: %s\n", $$1, $$2}'
	echo '                   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━'
	echo ''
	# Show provision flow

%:
	@:

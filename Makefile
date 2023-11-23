# ************************************* #
# ----- Trade app one Make File ------- #
# ************************************* #

init:
	@bash init.bash

# ----------------------- PHP --------------------------------------
dev: start-mysql start-php start-nginx start-mongo
	 @echo "Start dev in http://localhost:8080"

# ----------------------- PHP --------------------------------------
start-php:
	@docker-compose up -d php
stop-php:
	@docker-compose stop php

# ----------------------- NGINX --------------------------------------
start-nginx:
	@docker-compose up -d nginx
stop-nginx:
	@docker-compose stop nginx

# ----------------------- MYSQL ------------------------------------
start-mysql:
	@docker-compose up -d mysql 
stop-mysql:
	@docker-compose stop mysql

# ----------------------- ELASTICSEARCH ----------------------------
start-elasticsearch:
	@docker-compose up -d elasticsearch 
stop-elasticsearch:
	@docker-compose stop elasticsearch

# ----------------------- MONGO -----------------------------------
start-mongo:
	@docker-compose up -d mongo 
stop-mongo:
	@docker-compose stop mongo 

# ----------------------- KIBANA ----------------------------------
start-kibana:
	@docker-compose up -d kibana 
stop-kibana:
	@docker-compose stop kibana 

# ----------------------- REDIS -----------------------------------
start-redis:
	@docker-compose up -d redis 
stop-redis:
	@docker-compose stop redis 

start-all: start-mysql start-mongo start-elasticsearch start-kibana start-redis start-php start-ngnix

stop-all: stop-mysql stop-mongo stop-elasticsearch stop-kibana stop-redis stop-php stop-nginx

clean-containers:
	@docker-compose down

status-containers:
	@docker ps | grep trade-app-one

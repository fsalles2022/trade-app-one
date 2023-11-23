#!/bin/bash

function trade-app-one-php-exec() {
    COMMAND="$@"
    (docker exec -ti trade-app-one-php bash -c "${COMMAND}")
}

function trade-app-one-mongo-exec() {
    COMMAND="$@"
    (docker exec -ti trade-app-one-mongo bash -c "${COMMAND}")
}

function trade-app-one-mysql-exec() {
    COMMAND="$@"
    (docker exec -ti trade-app-one-mysql bash -c "${COMMAND}")
}

function phpunit-filter() {
    trade-app-one-php-exec "vendor/bin/phpunit --filter $@"
}

function phpunit-reload() {
    ag -l | entr -c docker exec -ti trade-app-one-php bash -c " vendor/bin/phpunit --stop-on-failure --filter $@"
}

function phpunit() {
    trade-app-one-php-exec "vendor/bin/phpunit $@"
}

function comp() {
    trade-app-one-php-exec "composer $@"
}

function art() {
    trade-app-one-php-exec "php artisan $@"
}

function remove-docker-mongo-db() {
    trade-app-one-mongo-exec 'mongo trade-app-one-local --eval "db.dropDatabase()"'
}

function recreate-mysql-database() {
    DATABASE='`trade-app-one-local`'
    trade-app-one-mysql-exec "mysql -u root -proot -e 'drop database ${DATABASE};'"
    trade-app-one-mysql-exec "mysql -u root -proot -e 'create database ${DATABASE};'"
}

function restore-dump-mysql-database() {
    DATABASE='trade-app-one-local'
    DUMP_NAME="backup.sql"
    DUMP_FILE_PATH="$(pwd)/database/dump/${DUMP_NAME}"
    COMMAND="mysql -h trade-app-one-mysql -u root -proot ${DATABASE} < /${DUMP_NAME}"

    docker run -ti -v "${DUMP_FILE_PATH}":/"${DUMP_NAME}" --network=tradeapponebackend_default mysql:5.7 bash -c "${COMMAND}"
}

function reset-database() {
    recreate-mysql-database
    remove-docker-mongo-db
    art migrate
    art db:seed
    echo '=)'
}
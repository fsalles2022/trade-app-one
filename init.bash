#!/bin/bash
# Unofficial Bash Strict Mode
set -e
set -u
set -o pipefail

# Initializing Docker containers necessary to minimal work.
make dev

# Inject script with functions to send commands to Docker instances.
source ./docker/commands.bash

# If not exists .env, copy .env.example.
if [ ! -f ./.env ]; then
    cp ./.env.example ./.env
fi

# Install Dependencies
composer install

# Migrate trade-app-one-mysql container.
art migrate

# Populate trade-app-one-local database.
art db:seed
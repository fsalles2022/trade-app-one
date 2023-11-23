[![buddy pipeline](https://app.buddy.works/tradeupgroup/trade-app-one-backend/pipelines/pipeline/131189/badge.svg?token=28b97f409bea6a9a5b6d6f6154ada176631f118eab1f696211d3b37aa1d5e8fd "buddy pipeline")](https://app.buddy.works/tradeupgroup/trade-app-one-backend/pipelines/pipeline/131189)

# Trade App One Backend 

This is a description on how setup the project configuring the application .env, 
mailtrap for integration tests, database and server, generating keys and run tests.

### Copy .env.example 

Copy the .env.example inside the project to .env

```bash
$ cp .env.example .env
```

### Configure Database and Server

Inside .env is configured Database connection, project use MongoDB that can be obtained in [TradeApp Docker Container](https://github.com/tradeupgroup/trade-app-one-docker) to run database and the server. 

### Configure Mail Tramp

Create an account in [MailTrap.io](https://mailtrap.io/) and update your .env with Mail keys.

The original .env looks like
```bash
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

After changes will looks like **(This example doesn't work)** 

```bash
MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=61d1d40m98bx1m
MAIL_PASSWORD=4v4t8y58iho321
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=joao.carlos1992@gmail.com
MAIL_FROM_NAME=carlos
```

### Install Dependencies

Then Install dependencies.
```bash
$ composer install
```

### Generate KEYS for Laravel

To generate your APP_KEY in .env.
```bash
$ php artisan key:generate
```

To generate your JWT_SECRET in .env.
```bash
$ php artisan jwt:secret
```

### Run Test Suite

Run tests with build-in phpunit.
```bash
$ vendor/bin/phpunit
```

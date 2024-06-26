Messenger
==================

![CI Status](https://github.com/Invis1ble/messenger/actions/workflows/ci.yml/badge.svg?event=push)
[![Code Coverage](https://codecov.io/gh/Invis1ble/messenger/graph/badge.svg?token=AQRIP417A4)](https://codecov.io/gh/Invis1ble/messenger)
[![Packagist](https://img.shields.io/packagist/v/Invis1ble/messenger.svg)](https://packagist.org/packages/Invis1ble/messenger)
[![MIT licensed](https://img.shields.io/badge/license-MIT-blue.svg)](./LICENSE)

Bus and Message Interfaces and Implementations.

- Command & Command Bus
- Query & Query Bus
- Event & Event Bus

Installation
------------

To install this package, you can use Composer:

```sh
composer require invis1ble/messenger
```

or just add it as a dependency in your `composer.json` file:

```json

{
    "require": {
        "invis1ble/messenger": "^5.0"
    }
}
```

After adding the above line, run the following command to install the package:

```sh
composer install
```


Development
-----------

### Getting started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up -d --wait` to start the Docker containers
4. Run `docker compose exec php composer install` to install dependencies
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

### Check for Coding Standards violations

Run PHP_CodeSniffer checks:

```sh
docker compose exec -it php bin/php_codesniffer
```

Run PHP-CS-Fixer checks:

```sh
docker compose exec -it php bin/php-cs-fixer
```


Testing
-------

To run Unit tests during development

```sh
docker compose exec php vendor/bin/phpunit
```

To run with coverage

```sh
XDEBUG_MODE=coverage docker compose up -d --wait
docker compose exec php vendor/bin/phpunit --coverage-clover var/log/coverage-clover.xml
```


License
-------

[The MIT License](./LICENSE)

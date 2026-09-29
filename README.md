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

Compatibility
-------------

| Symfony Messenger | PHP | CI coverage |
| --- | --- | --- |
| `^6.4` | 8.2+ | PHP 8.2 / Messenger 6.4 |
| `^7.0` | 8.2+ | PHP 8.2 / Messenger 7.0 and 7.4 |
| `^8.0` | 8.4+ (8.4.1+ for Messenger 8.1) | PHP 8.4 / Messenger 8.0 and 8.1; PHP 8.5 / latest stable 8.x |

Symfony Messenger 8.1.7 is the latest stable version verified for this update.
For new Symfony 8 applications, use PHP 8.5 and the maintained Symfony 8.1 series.
The Composer constraints continue to allow Symfony 6.4 and 7.x on PHP 8.2+.

The public bus APIs are unchanged. Queries require exactly one synchronous handler
and return its result, including `null` or `false`. Handler failures are unwrapped
to the first original exception; middleware exceptions pass through unchanged.
Middleware can add stamps to the underlying Symfony envelope as before.

The library's message and handler interfaces are marker interfaces independent of
Symfony's removed `MessageHandlerInterface`. Register handlers using Symfony's
`#[AsMessageHandler]` attribute, explicit configuration, or a compatible integration
bundle. Implementing a marker interface alone does not register a handler.

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
        "invis1ble/messenger": "^5.1"
    }
}
```

For a new project without `composer.lock`, install the dependencies:

```sh
composer install
```

For an existing project, use the `composer require` command above to update both
the manifest and its lock file.

To upgrade an existing installation for Symfony 8 after the 5.1 release:

```sh
composer require 'invis1ble/messenger:^5.1' 'symfony/messenger:^8.1' --with-all-dependencies
```

Run Composer on PHP 8.4.1 or later for Symfony 8.1. No platform requirement bypass
is needed. Any integration bundle must also declare compatible dependency ranges.


Development
-----------

### Getting started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up -d --wait` to start the Docker containers
4. Run `docker compose exec php composer install` to install dependencies
5. Run `docker compose down --remove-orphans` to stop the Docker containers.

The development image uses PHP 8.5. To check another supported PHP version, set
`PHP_VERSION` consistently when building and running Docker Compose, for example:

```sh
PHP_VERSION=8.4 docker compose build
PHP_VERSION=8.4 docker compose run --rm -T php composer install
PHP_VERSION=8.4 docker compose run --rm -T php composer check
```

For a clean dependency resolution, use a fresh checkout without `vendor/` or
`composer.lock`. CI resolves stable dependencies separately for every combination
in the compatibility table and checks the actual PHP platform requirements.

Run all package checks:

```sh
docker compose exec php composer check
```

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

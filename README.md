Messenger
==================

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
        "invis1ble/messenger": "^2.1"
    }
}
```

After adding the above line, run the following command to install the package:

```sh
composer install
```


Development
-----------

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/) (v2.10+)
2. Run `docker compose build --no-cache` to build fresh images
3. Run `docker compose up --pull always -d --wait` to start the Docker containers
4. Run `docker compose exec php composer install` to install dependencies
5. Run `docker compose down --remove-orphans` to stop the Docker containers.


Testing
-------

To run Unit tests during development

```sh
docker compose exec php vendor/bin/phpunit
```


License
-------

[The MIT License](./LICENSE)

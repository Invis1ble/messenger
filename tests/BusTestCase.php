<?php

declare(strict_types=1);

namespace Tests\Invis1ble\Messenger;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\TraceableMessageBus;

abstract class BusTestCase extends TestCase
{
    protected function createMessageBus(iterable $handlers = []): MessageBusInterface
    {
        return new TraceableMessageBus(new MessageBus([
            new HandleMessageMiddleware(
                handlersLocator: new HandlersLocator($handlers),
                allowNoHandlers: true,
            ),
        ]));
    }
}
<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests\Event;

use Invis1ble\Messenger\Event\EventBus;
use Invis1ble\Messenger\Event\EventInterface;
use Invis1ble\Messenger\Event\TraceableEventBus;
use Invis1ble\Messenger\Tests\BusTestCase;

class TraceableEventBusTest extends BusTestCase
{
    public function testRememberEvents(): void
    {
        $exception = new \DomainException('Test Exception');

        $event = $this->createMock(EventInterface::class);
        $eventBus = new TraceableEventBus(new EventBus($this->createMessageBus([
            $event::class => [
                function () use ($exception): void {
                    throw $exception;
                },
            ],
        ])));

        try {
            $eventBus->dispatch($event);
        } catch (\DomainException) {
            // do nothing
        }

        $dispatchedEvents = $eventBus->getDispatchedEvents();
        $this->assertCount(1, $dispatchedEvents);
        $this->assertArrayHasKey(0, $dispatchedEvents);
        $this->assertCount(1, $dispatchedEvents);
        $this->assertSame($event, $dispatchedEvents[0]['event']);
        $this->assertSame($exception, $dispatchedEvents[0]['exception']);
    }
}

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
        } catch (\DomainException $e) {
            // do nothing
        }

        $this->assertTrue(isset($e));
        $this->assertSame($exception::class, $e::class);
        $this->assertSame($exception->getMessage(), $e->getMessage());

        $dispatchedEvents = $eventBus->getDispatchedEvents();
        $this->assertCount(1, $dispatchedEvents);
        $this->assertArrayHasKey(0, $dispatchedEvents);
        $this->assertSame($event::class, $dispatchedEvents[0]->event::class);
        $this->assertSame($exception::class, $dispatchedEvents[0]->exception::class);
        $this->assertSame($exception->getMessage(), $dispatchedEvents[0]->exception->getMessage());
        $this->assertSame($exception->getCode(), $dispatchedEvents[0]->exception->getCode());
        $this->assertSame($exception->getTrace(), $dispatchedEvents[0]->exception->getTrace());
    }
}

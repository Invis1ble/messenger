<?php

declare(strict_types=1);

namespace Tests\Invis1ble\Messenger\Event;

use Invis1ble\Messenger\Event\EventBus;
use Invis1ble\Messenger\Event\EventInterface;
use Invis1ble\Messenger\Event\TraceableEventBus;
use Tests\Invis1ble\Messenger\BusTestCase;

class EventBusTest extends BusTestCase
{
    public function testDispatch(): void
    {
        $event = $this->createMock(EventInterface::class);
        $eventBus = $this->createEventBus();

        $eventBus->dispatch($event);

        $dispatchedEvents = $eventBus->getDispatchedEvents();
        $this->assertCount(1, $dispatchedEvents);
        $this->assertSame($event, $dispatchedEvents[0]['event']);
    }

    public function testDispatchThrowsException(): void
    {
        $exception = new \RuntimeException('Test exception');

        $event = $this->createMock(EventInterface::class);
        $eventBus = $this->createEventBus([
            $event::class => [
                function () use ($exception): void {
                    throw $exception;
                },
            ],
        ]);

        $this->expectExceptionObject($exception);
        $eventBus->dispatch($event);
    }

    private function createEventBus(iterable $handlers = []): TraceableEventBus
    {
        return new TraceableEventBus(
            new EventBus($this->createMessageBus($handlers)),
        );
    }
}

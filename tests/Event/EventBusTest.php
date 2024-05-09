<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests\Event;

use Invis1ble\Messenger\Event\EventBus;
use Invis1ble\Messenger\Event\EventInterface;
use Invis1ble\Messenger\Tests\BusTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBusTest extends BusTestCase
{
    public function testDelegateDispatchCallToUnderlyingBus(): void
    {
        $event = $this->createMock(EventInterface::class);
        $messageBus = $this->createMessageBus();
        $eventBus = $this->createEventBus($messageBus);

        $eventBus->dispatch($event);

        $dispatchedMessages = $messageBus->getDispatchedMessages();
        $this->assertCount(1, $dispatchedMessages);
        $this->assertArrayHasKey(0, $dispatchedMessages);
        $this->assertArrayHasKey('message', $dispatchedMessages[0]);
        $this->assertSame($event, $dispatchedMessages[0]['message']);
    }

    public function testThrowExceptionFromHandler(): void
    {
        $exception = new \DomainException('Test Exception');

        $event = $this->createMock(EventInterface::class);
        $eventBus = $this->createEventBus(
            messageBus: null,
            handlers: [
                $event::class => [
                    function () use ($exception): void {
                        throw $exception;
                    },
                ],
            ],
        );

        $this->expectExceptionObject($exception);
        $eventBus->dispatch($event);
    }

    private function createEventBus(?MessageBusInterface $messageBus, iterable $handlers = []): EventBus
    {
        if (null === $messageBus) {
            $messageBus = $this->createMessageBus($handlers);
        }

        return new EventBus($messageBus);
    }
}

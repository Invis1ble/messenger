<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Event;

use Invis1ble\Messenger\TraceableBus;

class TraceableEventBus extends TraceableBus implements EventBusInterface
{
    /**
     * @var TracedEvent[]
     */
    private array $dispatchedEvents = [];

    public function __construct(private readonly EventBusInterface $decoratedBus)
    {
    }

    public function dispatch(EventInterface $event): void
    {
        $callTime = new \DateTimeImmutable();
        $caller = $this->getCaller(EventBusInterface::class, 'dispatch');

        $tracedEvent = new TracedEvent($event, $caller, $callTime);
        $this->dispatchedEvents[] = $tracedEvent;

        try {
            $this->decoratedBus->dispatch($event);
        } catch (\Throwable $e) {
            $tracedEvent->setException($e);

            throw $e;
        }
    }

    /**
     * @return TracedEvent[]
     */
    public function getDispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }
}

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

        try {
            $this->decoratedBus->dispatch($event);
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            $this->dispatchedEvents[] = new TracedEvent($event, $caller, $callTime, $e ?? null);
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

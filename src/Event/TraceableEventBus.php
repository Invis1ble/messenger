<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Event;

use Invis1ble\Messenger\TraceableBus;

class TraceableEventBus extends TraceableBus implements EventBusInterface
{
    /**
     * @var EventInterface[]
     */
    private array $dispatchedEvents = [];

    public function __construct(private readonly EventBusInterface $decoratedBus)
    {
    }

    public function dispatch(EventInterface $event): void
    {
        $context = [
            'event' => $event,
            'caller' => $this->getCaller(EventBusInterface::class, 'dispatch'),
            'callTime' => microtime(true),
        ];

        try {
            $this->decoratedBus->dispatch($event);
        } catch (\Throwable $e) {
            $context['exception'] = $e;

            throw $e;
        } finally {
            $this->dispatchedEvents[] = $context;
        }
    }

    /**
     * @return EventInterface[]
     */
    public function getDispatchedEvents(): array
    {
        return $this->dispatchedEvents;
    }
}

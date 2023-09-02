<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Event;

use Invis1ble\Messenger\MessageBusExceptionTrait;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class EventBus implements EventBusInterface
{
    use MessageBusExceptionTrait;

    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(EventInterface $event): void
    {
        try {
            $this->messageBus->dispatch($event);
        } catch (HandlerFailedException $e) {
            $this->throwException($e);
        }
    }
}

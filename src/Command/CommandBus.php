<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Command;

use Invis1ble\Messenger\MessageBusExceptionTrait;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandBus implements CommandBusInterface
{
    use MessageBusExceptionTrait;

    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    public function dispatch(CommandInterface $command): void
    {
        try {
            $this->messageBus->dispatch($command);
        } catch (HandlerFailedException $e) {
            $this->throwException($e);
        }
    }
}

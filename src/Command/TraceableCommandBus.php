<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Command;

use Invis1ble\Messenger\TraceableBus;

class TraceableCommandBus extends TraceableBus implements CommandBusInterface
{
    /**
     * @var TracedCommand[]
     */
    private array $dispatchedCommands = [];

    public function __construct(private readonly CommandBusInterface $decoratedBus)
    {
    }

    public function dispatch(CommandInterface $command): void
    {
        $callTime = new \DateTimeImmutable();
        $caller = $this->getCaller(CommandBusInterface::class, 'dispatch');

        $this->dispatchedCommands[] = new TracedCommand($command, $caller, $callTime, $e ?? null);
        $this->decoratedBus->dispatch($command);
    }

    /**
     * @return TracedCommand[]
     */
    public function getDispatchedCommands(): array
    {
        return $this->dispatchedCommands;
    }
}

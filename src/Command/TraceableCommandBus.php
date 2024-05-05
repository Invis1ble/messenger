<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Command;

use Invis1ble\Messenger\TraceableBus;

class TraceableCommandBus extends TraceableBus implements CommandBusInterface
{
    /**
     * @var CommandInterface[]
     */
    private array $dispatchedCommands = [];

    public function __construct(private readonly CommandBusInterface $decoratedBus)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(CommandInterface $command): void
    {
        $context = [
            'command' => $command,
            'caller' => $this->getCaller(CommandBusInterface::class, 'dispatch'),
            'callTime' => microtime(true),
        ];

        try {
            $this->decoratedBus->dispatch($command);
        } catch (\Throwable $e) {
            $context['exception'] = $e;

            throw $e;
        } finally {
            $this->dispatchedCommands[] = $context;
        }
    }

    /**
     * @return CommandInterface[]
     */
    public function getDispatchedCommands(): array
    {
        return $this->dispatchedCommands;
    }
}

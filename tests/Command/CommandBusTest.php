<?php

declare(strict_types=1);

namespace Tests\Invis1ble\Messenger\Command;

use Invis1ble\Messenger\Command\CommandBus;
use Invis1ble\Messenger\Command\CommandInterface;
use Invis1ble\Messenger\Command\TraceableCommandBus;
use Tests\Invis1ble\Messenger\BusTestCase;

class CommandBusTest extends BusTestCase
{
    public function testDispatch(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $commandBus = $this->createCommandBus();

        $commandBus->dispatch($command);

        $dispatchedCommands = $commandBus->getDispatchedCommands();
        $this->assertCount(1, $dispatchedCommands);
        $this->assertSame($command, $dispatchedCommands[0]['command']);
    }

    public function testDispatchThrowsException(): void
    {
        $exception = new \RuntimeException('Test exception');

        $command = $this->createMock(CommandInterface::class);
        $commandBus = $this->createCommandBus([
            $command::class => [
                function () use ($exception): void {
                    throw $exception;
                },
            ],
        ]);

        $this->expectExceptionObject($exception);
        $commandBus->dispatch($command);
    }

    private function createCommandBus(iterable $handlers = []): TraceableCommandBus
    {
        return new TraceableCommandBus(
            new CommandBus($this->createMessageBus($handlers)),
        );
    }
}

<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests\Command;

use Invis1ble\Messenger\Command\CommandBus;
use Invis1ble\Messenger\Command\CommandInterface;
use Invis1ble\Messenger\Command\TraceableCommandBus;
use Invis1ble\Messenger\Tests\BusTestCase;

class TraceableCommandBusTest extends BusTestCase
{
    public function testRememberCommands(): void
    {
        $exception = new \DomainException('Test Exception');

        $command = $this->createMock(CommandInterface::class);
        $commandBus = new TraceableCommandBus(new CommandBus($this->createMessageBus([
            $command::class => [
                function () use ($exception): void {
                    throw $exception;
                },
            ],
        ])));

        try {
            $commandBus->dispatch($command);
        } catch (\DomainException) {}

        $dispatchedCommands = $commandBus->getDispatchedCommands();
        $this->assertCount(1, $dispatchedCommands);
        $this->assertArrayHasKey(0, $dispatchedCommands);
        $this->assertCount(1, $dispatchedCommands);
        $this->assertSame($command, $dispatchedCommands[0]['command']);
        $this->assertSame($exception, $dispatchedCommands[0]['exception']);
    }
}

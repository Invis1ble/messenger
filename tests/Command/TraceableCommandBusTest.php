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
        } catch (\DomainException $e) {
            // do nothing
        }

        $this->assertTrue(isset($e));
        $this->assertSame($exception::class, $e::class);
        $this->assertSame($exception->getMessage(), $e->getMessage());

        $dispatchedCommands = $commandBus->getDispatchedCommands();
        $this->assertCount(1, $dispatchedCommands);
        $this->assertArrayHasKey(0, $dispatchedCommands);
        $this->assertSame($command::class, $dispatchedCommands[0]->command::class);
        $this->assertSame($exception::class, $dispatchedCommands[0]->exception::class);
        $this->assertSame($exception->getMessage(), $dispatchedCommands[0]->exception->getMessage());
        $this->assertSame($exception->getCode(), $dispatchedCommands[0]->exception->getCode());
        $this->assertSame($exception->getTrace(), $dispatchedCommands[0]->exception->getTrace());
    }
}

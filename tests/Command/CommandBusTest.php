<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests\Command;

use Invis1ble\Messenger\Command\CommandBus;
use Invis1ble\Messenger\Command\CommandInterface;
use Invis1ble\Messenger\Tests\BusTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandBusTest extends BusTestCase
{
    public function testDelegateDispatchCallToUnderlyingBus(): void
    {
        $command = $this->createMock(CommandInterface::class);
        $messageBus = $this->createMessageBus();
        $commandBus = $this->createCommandBus($messageBus);

        $commandBus->dispatch($command);

        $dispatchedMessages = $messageBus->getDispatchedMessages();
        $this->assertCount(1, $dispatchedMessages);
        $this->assertArrayHasKey(0, $dispatchedMessages);
        $this->assertArrayHasKey('message', $dispatchedMessages[0]);
        $this->assertSame($command, $dispatchedMessages[0]['message']);
    }

    public function testThrowExceptionFromHandler(): void
    {
        $exception = new \DomainException('Test Exception');

        $command = $this->createMock(CommandInterface::class);
        $commandBus = $this->createCommandBus(
            messageBus: null,
            handlers: [
                $command::class => [
                    function () use ($exception): void {
                        throw $exception;
                    },
                ],
            ],
        );

        $this->expectExceptionObject($exception);
        $commandBus->dispatch($command);
    }

    private function createCommandBus(?MessageBusInterface $messageBus, iterable $handlers = []): CommandBus
    {
        if (null === $messageBus) {
            $messageBus = $this->createMessageBus($handlers);
        }

        return new CommandBus($messageBus);
    }
}

<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests\Query;

use Invis1ble\Messenger\Query\QueryBus;
use Invis1ble\Messenger\Query\QueryInterface;
use Invis1ble\Messenger\Tests\BusTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBusTest extends BusTestCase
{
    public function testDelegateDispatchCallToUnderlyingBus(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $messageBus = $this->createMessageBus([
            $query::class => [
                function () {
                    // do nothing
                },
            ],
        ]);
        $queryBus = $this->createQueryBus($messageBus);

        $queryBus->ask($query);

        $dispatchedMessages = $messageBus->getDispatchedMessages();
        $this->assertCount(1, $dispatchedMessages);
        $this->assertArrayHasKey(0, $dispatchedMessages);
        $this->assertArrayHasKey('message', $dispatchedMessages[0]);
        $this->assertSame($query, $dispatchedMessages[0]['message']);
    }

    public function testThrowExceptionFromHandler(): void
    {
        $exception = new \DomainException('Test Exception');

        $query = $this->createMock(QueryInterface::class);
        $queryBus = $this->createQueryBus(
            messageBus: null,
            handlers: [
                $query::class => [
                    function () use ($exception): void {
                        throw $exception;
                    },
                ],
            ],
        );

        $this->expectExceptionObject($exception);
        $queryBus->ask($query);
    }

    private function createQueryBus(?MessageBusInterface $messageBus, iterable $handlers = []): QueryBus
    {
        if (null === $messageBus) {
            $messageBus = $this->createMessageBus($handlers);
        }

        return new QueryBus($messageBus);
    }
}

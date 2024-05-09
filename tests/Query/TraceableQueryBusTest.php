<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests\Query;

use Invis1ble\Messenger\Query\QueryBus;
use Invis1ble\Messenger\Query\QueryInterface;
use Invis1ble\Messenger\Query\TraceableQueryBus;
use Invis1ble\Messenger\Tests\BusTestCase;

class TraceableQueryBusTest extends BusTestCase
{
    public function testRememberQueries(): void
    {
        $exception = new \DomainException('Test Exception');

        $query = $this->createMock(QueryInterface::class);
        $queryBus = new TraceableQueryBus(new QueryBus($this->createMessageBus([
            $query::class => [
                function () use ($exception): void {
                    throw $exception;
                },
            ],
        ])));

        try {
            $queryBus->ask($query);
        } catch (\DomainException) {}

        $dispatchedQueries = $queryBus->getAskedQueries();
        $this->assertCount(1, $dispatchedQueries);
        $this->assertArrayHasKey(0, $dispatchedQueries);
        $this->assertCount(1, $dispatchedQueries);
        $this->assertSame($query, $dispatchedQueries[0]['query']);
        $this->assertSame($exception, $dispatchedQueries[0]['exception']);
    }
}

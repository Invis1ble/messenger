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
        } catch (\DomainException $e) {
            // do nothing
        }

        $this->assertTrue(isset($e));
        $this->assertSame($exception::class, $e::class);
        $this->assertSame($exception->getMessage(), $e->getMessage());

        $dispatchedQueries = $queryBus->getAskedQueries();
        $this->assertCount(1, $dispatchedQueries);
        $this->assertArrayHasKey(0, $dispatchedQueries);
        $this->assertSame($query::class, $dispatchedQueries[0]->query::class);
        $this->assertSame($exception::class, $dispatchedQueries[0]->exception::class);
        $this->assertSame($exception->getMessage(), $dispatchedQueries[0]->exception->getMessage());
        $this->assertSame($exception->getCode(), $dispatchedQueries[0]->exception->getCode());
        $this->assertSame($exception->getTrace(), $dispatchedQueries[0]->exception->getTrace());
    }
}

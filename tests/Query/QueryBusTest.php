<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests\Query;

use Invis1ble\Messenger\Query\QueryBus;
use Invis1ble\Messenger\Query\QueryInterface;
use Invis1ble\Messenger\Query\TraceableQueryBus;
use Invis1ble\Messenger\Tests\BusTestCase;

class QueryBusTest extends BusTestCase
{
    public function testAsk(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $queryBus = $this->createQueryBus([
            $query::class => [
                function () {
                    // do nothing
                },
            ],
        ]);

        $queryBus->ask($query);

        $askedQueries = $queryBus->getAskedQueries();
        $this->assertCount(1, $askedQueries);
        $this->assertSame($query, $askedQueries[0]['query']);
    }

    public function testAskThrowsException(): void
    {
        $exception = new \RuntimeException('Test exception');

        $query = $this->createMock(QueryInterface::class);
        $eventBus = $this->createQueryBus([
            $query::class => [
                function () use ($exception): void {
                    throw $exception;
                },
            ],
        ]);

        $this->expectExceptionObject($exception);
        $eventBus->ask($query);
    }

    private function createQueryBus(iterable $handlers = []): TraceableQueryBus
    {
        return new TraceableQueryBus(
            new QueryBus($this->createMessageBus($handlers)),
        );
    }
}

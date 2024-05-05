<?php

declare(strict_types=1);

namespace Tests\Invis1ble\Messenger\Query;

use Invis1ble\Messenger\Query\QueryBus;
use Invis1ble\Messenger\Query\QueryInterface;
use Invis1ble\Messenger\Query\TraceableQueryBus;
use Tests\Invis1ble\Messenger\BusTestCase;

class QueryBusTest extends BusTestCase
{
    public function testAsk(): void
    {
        $query = $this->createMock(QueryInterface::class);
        $queryBus = $this->createQueryBus([$query::class => [function () {}]]);

        $queryBus->ask($query);

        $askedQueries = $queryBus->getAskedQueries();
        $this->assertCount(1, $askedQueries);
        $this->assertSame($query, $askedQueries[0]['query']);
    }

    private function createQueryBus(iterable $handlers = []): TraceableQueryBus
    {
        return new TraceableQueryBus(
            new QueryBus($this->createMessageBus($handlers)),
        );
    }
}

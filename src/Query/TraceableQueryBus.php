<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Query;

use Invis1ble\Messenger\TraceableBus;

class TraceableQueryBus extends TraceableBus implements QueryBusInterface
{
    /**
     * @var TracedQuery[]
     */
    private array $askedQueries = [];

    public function __construct(private readonly QueryBusInterface $decoratedBus)
    {
    }

    /**
     * @throws \Throwable
     */
    public function ask(QueryInterface $query): mixed
    {
        $callTime = new \DateTimeImmutable();
        $caller = $this->getCaller(QueryBusInterface::class, 'ask');

        $tracedQuery = new TracedQuery($query, $caller, $callTime);
        $this->askedQueries[] = $tracedQuery;

        try {
            return $this->decoratedBus->ask($query);
        } catch (\Throwable $e) {
            $tracedQuery->setException($e);
            throw $e;
        }
    }

    /**
     * @return TracedQuery[]
     */
    public function getAskedQueries(): array
    {
        return $this->askedQueries;
    }
}

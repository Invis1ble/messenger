<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Query;

use Invis1ble\Messenger\TraceableBus;

class TraceableQueryBus extends TraceableBus implements QueryBusInterface
{
    /**
     * @var QueryInterface[]
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
        $context = [
            'query' => $query,
            'caller' => $this->getCaller(QueryBusInterface::class, 'ask'),
            'callTime' => microtime(true),
        ];

        try {
            return $this->decoratedBus->ask($query);
        } catch (\Throwable $e) {
            $context['exception'] = $e;

            throw $e;
        } finally {
            $this->askedQueries[] = $context;
        }
    }

    /**
     * @return QueryInterface[]
     */
    public function getAskedQueries(): array
    {
        return $this->askedQueries;
    }
}

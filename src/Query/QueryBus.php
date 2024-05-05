<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Query;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function ask(QueryInterface $query): mixed
    {
        return $this->handle($query);
    }
}

<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Query;

use Invis1ble\Messenger\MessageBusExceptionTrait;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus implements QueryBusInterface
{
    use HandleTrait;
    use MessageBusExceptionTrait;

    public function __construct(private MessageBusInterface $messageBus)
    {
    }

    public function ask(QueryInterface $query): mixed
    {
        try {
            return $this->handle($query);
        } catch (HandlerFailedException $e) {
            $this->throwException($e);
        }
    }
}

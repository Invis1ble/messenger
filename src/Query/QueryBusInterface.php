<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Query;

interface QueryBusInterface
{
    /**
     * @throws \Throwable
     */
    public function ask(QueryInterface $query): mixed;
}

<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Query;

interface QueryBusInterface
{
    public function ask(QueryInterface $query);
}

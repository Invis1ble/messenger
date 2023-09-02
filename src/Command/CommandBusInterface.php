<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Command;

interface CommandBusInterface
{
    /**
     * @throws \Throwable
     */
    public function dispatch(CommandInterface $command): void;
}

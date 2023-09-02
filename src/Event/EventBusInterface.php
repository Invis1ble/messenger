<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Event;

interface EventBusInterface
{
    /**
     * @throws \Throwable
     */
    public function dispatch(EventInterface $event): void;
}

<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Event;

use Invis1ble\Messenger\AbstractTracedMessage;

final readonly class TracedEvent extends AbstractTracedMessage
{
    public function __construct(
        public EventInterface $event,
        array $caller,
        \DateTimeImmutable $callTime,
        ?\Throwable $exception = null,
    ) {
        parent::__construct($caller, $callTime, $exception);
    }
}

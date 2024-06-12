<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Command;

use Invis1ble\Messenger\AbstractTracedMessage;

final class TracedCommand extends AbstractTracedMessage
{
    public function __construct(
        public readonly CommandInterface $command,
        array $caller,
        \DateTimeImmutable $callTime,
        ?\Throwable $exception = null,
    ) {
        parent::__construct($caller, $callTime, $exception);
    }
}

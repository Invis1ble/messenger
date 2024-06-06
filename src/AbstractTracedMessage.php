<?php

declare(strict_types=1);

namespace Invis1ble\Messenger;

abstract readonly class AbstractTracedMessage
{
    public function __construct(
        public array $caller,
        public \DateTimeImmutable $callTime,
        public ?\Throwable $exception = null,
    ) {
    }
}

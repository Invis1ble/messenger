<?php

declare(strict_types=1);

namespace Invis1ble\Messenger;

abstract class AbstractTracedMessage
{
    public function __construct(
        public readonly array $caller,
        public readonly \DateTimeImmutable $callTime,
        public ?\Throwable $exception = null,
    ) {
    }

    public function setException(\Throwable $exception): void
    {
        $this->exception = $exception;
    }
}

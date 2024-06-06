<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Query;

use Invis1ble\Messenger\AbstractTracedMessage;

final readonly class TracedQuery extends AbstractTracedMessage
{
    public function __construct(
        public QueryInterface $query,
        array $caller,
        \DateTimeImmutable $callTime,
        ?\Throwable $exception = null,
    ) {
        parent::__construct($caller, $callTime, $exception);
    }
}

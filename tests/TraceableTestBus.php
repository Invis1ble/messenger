<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests;

use Invis1ble\Messenger\TraceableBus;

final class TraceableTestBus extends TraceableBus
{
    public function dispatch(): array
    {
        return $this->additionalMethodInBacktrace();
    }

    private function additionalMethodInBacktrace(): array
    {
        return $this->getCaller(self::class, 'dispatch');
    }
}
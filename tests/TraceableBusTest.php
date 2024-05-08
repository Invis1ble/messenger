<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests;

use PHPUnit\Framework\TestCase;

class TraceableBusTest extends TestCase
{
    public function testGetCallerWithAdditionalMethodInBacktrace(): void
    {
        $bus = $this->createBus();
        $caller = $bus->dispatch();

        $this->assertSame([
            'name' => 'TraceableBusTest.php',
            'file' => __FILE__,
            'line' => 14,
        ], $caller);
    }

    public function testGetCallerWithExtraFunctionCallInBacktrace(): void
    {
        $bus = $this->createBus();
        $caller = dispatch($bus);

        $this->assertSame([
            'name' => 'TraceableBusTest.php',
            'file' => __FILE__,
            'line' => 26,
        ], $caller);
    }

    private function createBus(): TraceableTestBus
    {
        return new TraceableTestBus();
    }
}

if (!function_exists(__NAMESPACE__ . '\dispatch')) {
    function dispatch(TraceableTestBus $bus): array
    {
        return $bus->dispatch();
    }
}

<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests;

use Invis1ble\Messenger\TraceableBus;
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
            'line' => 15,
        ], $caller);
    }

    public function testGetCallerWithExtraFunctionCallInBacktrace(): void
    {
        $bus = $this->createBus();
        $caller = dispatch($bus);

        $this->assertSame([
            'name' => 'TraceableBusTest.php',
            'file' => __FILE__,
            'line' => 27,
        ], $caller);
    }

    private function createBus(): TraceableTestBus
    {
        return new TraceableTestBus();
    }
}

/**
 * @internal
 */
// phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
// phpcs:disable Symfony.Classes.MultipleClassesOneFile
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
// phpcs:enable PSR1.Classes.ClassDeclaration.MultipleClassesOneFile
// phpcs:enable Symfony.Classes.MultipleClassesOneFile

if (!function_exists(__NAMESPACE__ . '\dispatch')) {
    /**
     * @internal
     */
    function dispatch(TraceableTestBus $bus): array
    {
        return $bus->dispatch();
    }
}

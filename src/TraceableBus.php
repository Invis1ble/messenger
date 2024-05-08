<?php

declare(strict_types=1);

namespace Invis1ble\Messenger;

abstract class TraceableBus
{
    protected function getCaller(string $busClass, string $busMethod): array
    {
        $trace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 8);

        $file = $trace[1]['file'] ?? null;
        $line = $trace[1]['line'] ?? null;

        for ($i = 2; $i < 8; ++$i) {
            if (isset($trace[$i]['class'], $trace[$i]['function'])
                && $busMethod === $trace[$i]['function']
                && is_a($trace[$i]['class'], $busClass, true)
            ) {
                $file = $trace[$i]['file'];
                $line = $trace[$i]['line'];

                while (++$i < 8) {
                    if (isset($trace[$i]['function'], $trace[$i]['file']) && empty($trace[$i]['class']) && !str_starts_with($trace[$i]['function'], 'call_user_func')) {
                        $file = $trace[$i]['file'];
                        $line = $trace[$i]['line'];

                        break;
                    }
                }
                break;
            }
        }

        $name = str_replace('\\', '/', (string) $file);

        return [
            'name' => substr($name, strrpos($name, '/') + 1),
            'file' => $file,
            'line' => $line,
        ];
    }
}

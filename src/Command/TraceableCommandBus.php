<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Command;

use Symfony\Component\Messenger\HandleTrait;

class TraceableCommandBus implements CommandBusInterface
{
    /**
     * @var CommandInterface[]
     */
    private array $dispatchedCommands = [];

    public function __construct(private readonly CommandBusInterface $decoratedBus)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(CommandInterface $command): void
    {
        $context = [
            'command' => $command,
            'caller' => $this->getCaller(),
            'callTime' => microtime(true),
        ];

        try {
            $this->decoratedBus->dispatch($command);
        } catch (\Throwable $e) {
            $context['exception'] = $e;

            throw $e;
        } finally {
            $this->dispatchedCommands[] = $context;
        }
    }

    /**
     * @return CommandInterface[]
     */
    public function getDispatchedCommands(): array
    {
        return $this->dispatchedCommands;
    }

    private function getCaller(): array
    {
        $trace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 8);

        $file = $trace[1]['file'] ?? null;
        $line = $trace[1]['line'] ?? null;

        $handleTraitFile = (new \ReflectionClass(HandleTrait::class))->getFileName();
        $found = false;
        for ($i = 1; $i < 8; ++$i) {
            if (isset($trace[$i]['file'], $trace[$i + 1]['file'], $trace[$i + 1]['line']) && $trace[$i]['file'] === $handleTraitFile) {
                $file = $trace[$i + 1]['file'];
                $line = $trace[$i + 1]['line'];
                $found = true;

                break;
            }
        }

        for ($i = 2; $i < 8 && !$found; ++$i) {
            if (isset($trace[$i]['class'], $trace[$i]['function'])
                && 'dispatch' === $trace[$i]['function']
                && is_a($trace[$i]['class'], CommandBusInterface::class, true)
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

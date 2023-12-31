<?php

declare(strict_types=1);

namespace Invis1ble\Messenger;

use Symfony\Component\Messenger\Exception\HandlerFailedException;

trait MessageBusExceptionTrait
{
    /**
     * @throws \Throwable
     */
    public function throwException(HandlerFailedException $exception): void
    {
        while ($exception instanceof HandlerFailedException) {
            /** @var \Throwable $exception */
            $exception = $exception->getPrevious();
        }

        throw $exception;
    }
}

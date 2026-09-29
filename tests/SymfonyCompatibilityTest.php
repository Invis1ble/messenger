<?php

declare(strict_types=1);

namespace Invis1ble\Messenger\Tests;

use Invis1ble\Messenger\Command\CommandBus;
use Invis1ble\Messenger\Command\CommandHandlerInterface;
use Invis1ble\Messenger\Command\CommandInterface;
use Invis1ble\Messenger\Command\TraceableCommandBus;
use Invis1ble\Messenger\Event\EventBus;
use Invis1ble\Messenger\Event\EventHandlerInterface;
use Invis1ble\Messenger\Event\EventInterface;
use Invis1ble\Messenger\Event\TraceableEventBus;
use Invis1ble\Messenger\MessageHandlerInterface;
use Invis1ble\Messenger\MessageInterface;
use Invis1ble\Messenger\Query\QueryBus;
use Invis1ble\Messenger\Query\QueryBusInterface;
use Invis1ble\Messenger\Query\QueryHandlerInterface;
use Invis1ble\Messenger\Query\QueryInterface;
use Invis1ble\Messenger\Query\TraceableQueryBus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\Handler\HandlerDescriptor;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Middleware\AddBusNameStampMiddleware;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\BusNameStamp;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\Stamp\HandlerArgumentsStamp;

class SymfonyCompatibilityTest extends TestCase
{
    #[DataProvider('busTypes')]
    public function testDispatchThroughMiddlewareAndMarkerHandlers(string $type, bool $traceable): void
    {
        $message = $this->createMessage($type);
        $result = new \stdClass();
        $context = new \stdClass();
        $handler = new class($result) implements CommandHandlerInterface, EventHandlerInterface, QueryHandlerInterface {
            public ?MessageInterface $message = null;
            public ?object $context = null;

            public function __construct(private readonly object $result)
            {
            }

            public function __invoke(MessageInterface $message, object $context): object
            {
                $this->message = $message;
                $this->context = $context;

                return $this->result;
            }
        };
        $middleware = new class($context) implements MiddlewareInterface {
            public ?Envelope $envelope = null;

            public function __construct(private readonly object $context)
            {
            }

            public function handle(Envelope $envelope, StackInterface $stack): Envelope
            {
                $envelope = $envelope->with(new HandlerArgumentsStamp([$this->context]));
                $this->envelope = $stack->next()->handle($envelope, $stack);

                return $this->envelope;
            }
        };
        $messageInterface = match ($type) {
            'command' => CommandInterface::class,
            'event' => EventInterface::class,
            'query' => QueryInterface::class,
        };
        $bus = $this->createBus($type, $traceable, new MessageBus([
            new AddBusNameStampMiddleware($type),
            $middleware,
            new HandleMessageMiddleware(new HandlersLocator([$messageInterface => [$handler]])),
        ]));

        $actual = $this->dispatch($bus, $message);

        self::assertSame('query' === $type ? $result : null, $actual);
        self::assertInstanceOf(MessageHandlerInterface::class, $handler);
        self::assertSame($message, $handler->message);
        self::assertSame($context, $handler->context);
        self::assertSame($message, $middleware->envelope->getMessage());
        self::assertSame($type, $middleware->envelope->last(BusNameStamp::class)->getBusName());
        self::assertSame([$context], $middleware->envelope->last(HandlerArgumentsStamp::class)->getAdditionalArguments());
        self::assertCount(1, $middleware->envelope->all(HandledStamp::class));
        self::assertSame($result, $middleware->envelope->last(HandledStamp::class)->getResult());
    }

    #[DataProvider('busTypes')]
    public function testUnwrapNestedHandlerFailures(string $type, bool $traceable): void
    {
        $message = $this->createMessage($type);
        $failure = new \Error('Original handler failure');
        $wrapped = new HandlerFailedException(new Envelope($message), [$failure]);
        $bus = $this->createBus($type, $traceable, new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                MessageInterface::class => [
                    static function () use ($wrapped): never {
                        throw $wrapped;
                    },
                ],
            ])),
        ]));

        $this->assertDispatchThrowsSame($bus, $message, $failure);
    }

    #[DataProvider('busTypes')]
    public function testRethrowFirstFailureWhenMultipleHandlersFail(string $type, bool $traceable): void
    {
        $message = $this->createMessage($type);
        $firstFailure = new \DomainException('First handler failure');
        $secondFailure = new \RuntimeException('Second handler failure');
        $calls = [];
        $bus = $this->createBus($type, $traceable, new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                MessageInterface::class => [
                    new HandlerDescriptor(static function () use ($firstFailure, &$calls): never {
                        $calls[] = 'first';

                        throw $firstFailure;
                    }, ['alias' => 'first']),
                    new HandlerDescriptor(static function () use ($secondFailure, &$calls): never {
                        $calls[] = 'second';

                        throw $secondFailure;
                    }, ['alias' => 'second']),
                ],
            ])),
        ]));

        $this->assertDispatchThrowsSame($bus, $message, $firstFailure);
        self::assertSame(['first', 'second'], $calls);
    }

    #[DataProvider('busTypes')]
    public function testPreserveMiddlewareFailures(string $type, bool $traceable): void
    {
        $message = $this->createMessage($type);
        $failure = new \RuntimeException('Middleware failure');
        $middleware = new class($failure) implements MiddlewareInterface {
            public function __construct(private readonly \Throwable $failure)
            {
            }

            public function handle(Envelope $envelope, StackInterface $stack): Envelope
            {
                throw $this->failure;
            }
        };
        $bus = $this->createBus($type, $traceable, new MessageBus([$middleware]));

        $this->assertDispatchThrowsSame($bus, $message, $failure);
    }

    #[DataProvider('busTypes')]
    public function testPreserveMissingHandlerErrors(string $type, bool $traceable): void
    {
        $bus = $this->createBus($type, $traceable, new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([])),
        ]));

        $this->expectException(NoHandlerForMessageException::class);
        $this->dispatch($bus, $this->createMessage($type));
    }

    #[DataProvider('queryHandlerCounts')]
    public function testRequireExactlyOneQueryHandler(int $handlerCount, bool $traceable): void
    {
        $handlers = 0 === $handlerCount ? [] : [
            new HandlerDescriptor(static fn () => 'first', ['alias' => 'first']),
            new HandlerDescriptor(static fn () => 'second', ['alias' => 'second']),
        ];
        $bus = $this->createBus('query', $traceable, new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([QueryInterface::class => $handlers]), true),
        ]));

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(0 === $handlerCount ? 'handled zero times' : 'handled multiple times');
        $this->dispatch($bus, $this->createMessage('query'));
    }

    #[DataProvider('queryResults')]
    public function testReturnQueryResultUnchanged(mixed $result, bool $traceable): void
    {
        $bus = $this->createBus('query', $traceable, new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                QueryInterface::class => [static fn () => $result],
            ])),
        ]));

        self::assertSame($result, $this->dispatch($bus, $this->createMessage('query')));
    }

    public static function busTypes(): iterable
    {
        foreach (['command', 'event', 'query'] as $type) {
            yield $type => [$type, false];
            yield 'traceable ' . $type => [$type, true];
        }
    }

    public static function queryHandlerCounts(): iterable
    {
        foreach ([0, 2] as $count) {
            yield $count . ' handlers' => [$count, false];
            yield 'traceable ' . $count . ' handlers' => [$count, true];
        }
    }

    public static function queryResults(): iterable
    {
        foreach ([null, false, 0, '', ['answer' => 42]] as $result) {
            yield [$result, false];
            yield [$result, true];
        }
    }

    private function createMessage(string $type): MessageInterface
    {
        return match ($type) {
            'command' => new class() implements CommandInterface {
            },
            'event' => new class() implements EventInterface {
            },
            'query' => new class() implements QueryInterface {
            },
        };
    }

    private function createBus(string $type, bool $traceable, MessageBusInterface $messageBus): object
    {
        return match ($type) {
            'command' => $traceable ? new TraceableCommandBus(new CommandBus($messageBus)) : new CommandBus($messageBus),
            'event' => $traceable ? new TraceableEventBus(new EventBus($messageBus)) : new EventBus($messageBus),
            'query' => $traceable ? new TraceableQueryBus(new QueryBus($messageBus)) : new QueryBus($messageBus),
        };
    }

    private function dispatch(object $bus, MessageInterface $message): mixed
    {
        if ($bus instanceof QueryBusInterface) {
            return $bus->ask($message);
        }

        $bus->dispatch($message);

        return null;
    }

    private function assertDispatchThrowsSame(object $bus, MessageInterface $message, \Throwable $expected): void
    {
        try {
            $this->dispatch($bus, $message);
        } catch (\Throwable $actual) {
            self::assertSame($expected, $actual);

            return;
        }

        self::fail('Dispatch did not throw the expected exception.');
    }
}

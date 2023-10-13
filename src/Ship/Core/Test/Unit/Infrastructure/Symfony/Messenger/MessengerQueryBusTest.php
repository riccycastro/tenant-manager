<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Infrastructure\Symfony\Messenger;

use App\Ship\Core\Application\QueryHandler\QueryBusInterface;
use App\Ship\Core\Domain\Query\QueryInterface;
use App\Ship\Core\Infrastructure\Symfony\Messenger\MessengerQueryBus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @covers \App\Ship\Core\Infrastructure\Symfony\Messenger\MessengerQueryBus
 */
final class MessengerQueryBusTest extends TestCase
{
    use ProphecyTrait;

    private MessengerQueryBus $sut;
    private MessageBusInterface|ObjectProphecy $queryBus;

    public function testItIsQueryBusInterface(): void
    {
        self::assertInstanceOf(QueryBusInterface::class, $this->sut);
    }

    public function testQueryBusDispatchMethodIsCalledOnDispatch(): void
    {
        $envelop = new Envelope(new class() {
        }, [
            new HandledStamp('1', 'A handler name'),
        ]);

        $query = new class() implements QueryInterface {
        };

        $this->queryBus
            ->dispatch($query)
            ->shouldBeCalled()
            ->willReturn($envelop)
        ;

        $result = $this->sut->ask($query);

        self::assertEquals('1', $result);
    }

    public function testHandlerFailedExceptionIsCatch(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Handler true exception');

        $envelop = new Envelope(new class() {
        }, [
            new HandledStamp('1', 'A handler name'),
        ]);

        $query = new class() implements QueryInterface {
        };

        $this->queryBus
            ->dispatch($query)
            ->shouldBeCalled()
            ->willThrow(new HandlerFailedException($envelop, [new \Exception('Handler true exception')]))
        ;

        $this->sut->ask($query);
    }

    protected function setUp(): void
    {
        $this->queryBus = $this->prophesize(MessageBusInterface::class);

        $this->sut = new MessengerQueryBus(
            $this->queryBus->reveal(),
        );
    }
}

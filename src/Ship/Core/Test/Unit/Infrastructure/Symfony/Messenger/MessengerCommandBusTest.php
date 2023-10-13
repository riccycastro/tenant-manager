<?php

declare(strict_types=1);

namespace App\Ship\Core\Test\Unit\Infrastructure\Symfony\Messenger;

use App\Ship\Core\Application\CommandHandler\CommandBusInterface;
use App\Ship\Core\Domain\Command\CommandInterface;
use App\Ship\Core\Infrastructure\Symfony\Messenger\MessengerCommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

/**
 * @covers \App\Ship\Core\Infrastructure\Symfony\Messenger\MessengerCommandBus
 */
final class MessengerCommandBusTest extends TestCase
{
    use ProphecyTrait;

    private MessengerCommandBus $sut;
    private MessageBusInterface|ObjectProphecy $commandBus;

    public function testItIsCommandBusInterface(): void
    {
        self::assertInstanceOf(CommandBusInterface::class, $this->sut);
    }

    public function testCommandBusDispatchMethodIsCalledOnDispatch(): void
    {
        $envelop = new Envelope(new class() {
        }, [
            new HandledStamp('1', 'A handler name'),
        ]);

        $command = new class() implements CommandInterface {
        };

        $this->commandBus
            ->dispatch($command)
            ->shouldBeCalled()
            ->willReturn($envelop)
        ;

        $result = $this->sut->dispatch($command);

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

        $command = new class() implements CommandInterface {
        };

        $this->commandBus
            ->dispatch($command)
            ->shouldBeCalled()
            ->willThrow(new HandlerFailedException($envelop, [new \Exception('Handler true exception')]))
        ;

        $this->sut->dispatch($command);
    }

    protected function setUp(): void
    {
        $this->commandBus = $this->prophesize(MessageBusInterface::class);

        $this->sut = new MessengerCommandBus(
            $this->commandBus->reveal(),
        );
    }
}

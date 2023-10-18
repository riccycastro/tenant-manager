<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Test\Unit\Infrastructure\Listener;

use App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent;
use App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Infrastructure\Listener\TenantCreatedEventListener;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @covers \App\Containers\TenantContainer\Infrastructure\Listener\TenantCreatedEventListener
 *
 * @uses \App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent
 * @uses \App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage
 * @uses \App\Containers\TenantContainer\Domain\ValueObject\TenantCode
 */
final class TenantCreatedEventListenerTest extends TestCase
{
    use ProphecyTrait;

    public function testMessageBusDispatchOnInvoke(): void
    {
        $messageBus = $this->prophesize(MessageBusInterface::class);

        $messageBus
            ->dispatch(Argument::type(CreateTenantDatabaseMessage::class))
            ->shouldBeCalledOnce()
            ->willReturn(
                new Envelope(new class() {
                })
            )
        ;

        $sut = new TenantCreatedEventListener(
            $messageBus->reveal(),
        );

        ($sut)(new TenantCreatedEvent(
            TenantCode::fromString('vukix'),
        ));
    }
}

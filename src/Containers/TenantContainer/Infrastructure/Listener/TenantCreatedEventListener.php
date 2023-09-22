<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Listener;

use App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent;
use App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEventListener]
final class TenantCreatedEventListener
{
    public function __construct(
        private readonly MessageBusInterface $messageBus
    ) {
    }

    public function __invoke(TenantCreatedEvent $event): void
    {
        $this->messageBus->dispatch(new CreateTenantDatabaseMessage($event->tenantCode));
    }
}

<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\MessageHandler;

use App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage;
use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Ship\Core\Application\QueryHandler\QueryBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTenantDatabaseMessageHandler
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
    ) {
    }

    public function __invoke(CreateTenantDatabaseMessage $message): void
    {
        $tenantCode = $message->tenantCode;

        $tenant = $this->queryBus->ask(new FindTenantQuery(code: $message->tenantCode));

        var_dump($tenant);
    }
}

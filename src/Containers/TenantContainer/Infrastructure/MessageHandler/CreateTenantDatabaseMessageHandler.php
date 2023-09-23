<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\MessageHandler;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage;
use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Containers\TenantContainer\Infrastructure\Service\TenantDatabaseService;
use App\Ship\Core\Application\QueryHandler\QueryBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTenantDatabaseMessageHandler
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly TenantDatabaseService $tenantDatabaseService,
    ) {
    }

    public function __invoke(CreateTenantDatabaseMessage $message): void
    {
        $tenantCode = $message->tenantCode;

        $tenant = $this->queryBus->ask(new FindTenantQuery(code: $message->tenantCode));

        if (TenantStatus::WAITING_PROVISIONING !== $tenant->getStatus()) {
            // todo@log here
            return;
        }

        if ($this->tenantDatabaseService->databaseExists($tenantCode)) {
            // todo@log here
            // if database exists we update status to ready for migration
            // $this->updateTenantStatusAction->run($tenant, Tenant::STATUS_READY_FOR_MIGRATION);
            return;
        }
    }
}

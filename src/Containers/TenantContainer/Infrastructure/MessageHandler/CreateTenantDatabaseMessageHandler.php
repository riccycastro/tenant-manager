<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\MessageHandler;

use App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Infrastructure\Service\TenantDatabaseService;
use App\Ship\Core\Application\CommandHandler\CommandBusInterface;
use App\Ship\Core\Application\QueryHandler\QueryBusInterface;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTenantDatabaseMessageHandler
{
    public function __construct(
        private readonly QueryBusInterface $queryBus,
        private readonly CommandBusInterface $commandBus,
        private readonly TenantDatabaseService $tenantDatabaseService,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(CreateTenantDatabaseMessage $message): void
    {
        $tenant = $this->queryBus->ask(new FindTenantQuery(code: $message->tenantCode));

        assert($tenant instanceof Tenant);

        if (TenantStatus::WAITING_PROVISIONING !== $tenant->getStatus()) {
            // todo@log here
            return;
        }

        if ($this->tenantDatabaseService->databaseExists($tenant->getCode())) {
            // todo@log here
            // if database exists we update status to ready for migration
            $this->updateTenantStatus($tenant->getCode(), TenantStatus::READY_FOR_MIGRATION);

            return;
        }

        $this->tenantDatabaseService->beginDatabaseTransaction();
        try {
            $this->updateTenantStatus($tenant->getCode(), TenantStatus::PROVISIONING);

            $this->tenantDatabaseService->createDatabase($tenant->getCode());

            $generatedTenantDatabasePassword = UuidV4::uuid4()->toString();

            $this->tenantDatabaseService->createTenantDatabaseUser($tenant->getCode(), $generatedTenantDatabasePassword);

            $this->updateTenantStatus($tenant->getCode(), TenantStatus::READY_FOR_MIGRATION);
            $this->tenantDatabaseService->commitDatabaseTransaction();
        } catch (\Throwable $throwable) {
            $this->tenantDatabaseService->rollbackDatabaseTransaction();
            // todo@log here
            $this->updateTenantStatus($tenant->getCode(), TenantStatus::WAITING_PROVISIONING);
        }
    }

    private function updateTenantStatus(TenantCode $code, TenantStatus $status): void
    {
        $this->commandBus->dispatch(
            new UpdateTenantCommand(
                $code,
                status: $status
            )
        );
    }
}

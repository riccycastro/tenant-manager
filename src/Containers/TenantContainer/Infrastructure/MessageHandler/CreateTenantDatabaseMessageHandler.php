<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\MessageHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\Command\ProcessTenantPropertyCommand;
use App\Containers\TenantContainer\Domain\Command\UpdateTenantCommand;
use App\Containers\TenantContainer\Domain\Enum\PropertyType;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Exception\InvalidPropertyTypeException;
use App\Containers\TenantContainer\Domain\Message\CreateTenantDatabaseMessage;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyName;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyValue;
use App\Containers\TenantContainer\Infrastructure\Enum\TenantProperty;
use App\Containers\TenantContainer\Infrastructure\Service\DatabaseServiceInterface;
use App\Ship\Core\Application\CommandHandler\CommandBusInterface;
use App\Ship\Core\Application\Context;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTenantDatabaseMessageHandler
{
    private Connection $connection;

    public function __construct(
        private readonly FindsTenantInterface $findsTenant,
        private readonly CommandBusInterface $commandBus,
        private readonly DatabaseServiceInterface $tenantDatabaseService,
        private readonly Context $context,
        ManagerRegistry $doctrine,
    ) {
        $this->connection = $doctrine->getConnection('default'); // @phpstan-ignore-line
    }

    /**
     * @throws \Exception
     */
    public function __invoke(CreateTenantDatabaseMessage $message): void
    {
        $tenant = $this->findsTenant->withCode($message->tenantCode)->getResult();

        assert($tenant instanceof Tenant);

        if (TenantStatus::WAITING_PROVISIONING !== $tenant->getStatus()) {
            // todo@log here
            return;
        }

        if ($this->databaseExists($tenant)) {
            // todo@log here
            // if database exists we update status to ready for migration
            $this->updateTenantStatus($tenant->getCode(), TenantStatus::READY_FOR_MIGRATION);

            return;
        }

        $tenant = $this->updateTenantStatus($tenant->getCode(), TenantStatus::PROVISIONING);

        $isDatabaseCredentialsRegenerated = false;

        if (!($tenant->hasProperty(TenantPropertyName::fromString(TenantProperty::DATABASE_NAME->name))
            && $tenant->hasProperty(TenantPropertyName::fromString(TenantProperty::DATABASE_USER->name))
            && $tenant->hasProperty(TenantPropertyName::fromString(TenantProperty::DATABASE_PASSWORD->name)))
        ) {
            $isDatabaseCredentialsRegenerated = true;
            $this->connection->beginTransaction();
            try {
                $generatedTenantDatabasePassword = UuidV4::uuid4()->toString();

                $tenant = $this->createTenantProperty(
                    $tenant,
                    TenantProperty::DATABASE_NAME->name,
                    $this->tenantDatabaseService->generateDatabaseName($tenant->getCode())
                );
                $tenant = $this->createTenantProperty(
                    $tenant,
                    TenantProperty::DATABASE_USER->name,
                    $tenant->getCode()->toString()
                );
                $tenant = $this->createTenantProperty(
                    $tenant,
                    TenantProperty::DATABASE_PASSWORD->name,
                    $generatedTenantDatabasePassword
                );

                $this->connection->commit();
            } catch (\Throwable $throwable) {
                $this->connection->rollBack();
                $this->updateTenantStatus($tenant->getCode(), TenantStatus::WAITING_PROVISIONING);

                throw $throwable;
            }
        }

        try {
            if ($isDatabaseCredentialsRegenerated || !$this->tenantDatabaseService->hasDatabase($tenant->getCode())) {
                $this->tenantDatabaseService->createDatabase($tenant->getCode());
            }

            echo $isDatabaseCredentialsRegenerated ? '1' : '0';
            echo $this->tenantDatabaseService->hasUser($tenant->getCode()) ? '1' : '0';

            if ($isDatabaseCredentialsRegenerated || !$this->tenantDatabaseService->hasUser($tenant->getCode())) {
                $this->tenantDatabaseService->createTenantDatabaseUser(
                    $tenant->getCode(),
                    (string) $tenant->getProperty( // @phpstan-ignore-line
                        TenantPropertyName::fromString(TenantProperty::DATABASE_PASSWORD->name)
                    )->getValue(),
                );
            }
        } catch (\Throwable $throwable) {
            // todo@log here
            echo $throwable->getMessage();
            $this->updateTenantStatus($tenant->getCode(), TenantStatus::WAITING_PROVISIONING);
            throw $throwable;
        }

        $this->updateTenantStatus($tenant->getCode(), TenantStatus::READY_FOR_MIGRATION);
    }

    private function databaseExists(Tenant $tenant): bool
    {
        return $this->tenantDatabaseService->hasDatabase($tenant->getCode())
            && $this->tenantDatabaseService->hasUser($tenant->getCode())
            && $tenant->hasProperty(TenantPropertyName::fromString(TenantProperty::DATABASE_NAME->name))
            && $tenant->hasProperty(TenantPropertyName::fromString(TenantProperty::DATABASE_USER->name))
            && $tenant->hasProperty(TenantPropertyName::fromString(TenantProperty::DATABASE_PASSWORD->name));
    }

    private function updateTenantStatus(TenantCode $code, TenantStatus $status): Tenant
    {
        return $this->commandBus->dispatch(
            new UpdateTenantCommand(
                $code,
                status: $status
            )
        );
    }

    /**
     * @throws InvalidPropertyTypeException
     */
    private function createTenantProperty(Tenant $tenant, string $name, string $value): Tenant
    {
        return $this->commandBus->dispatch(
            new ProcessTenantPropertyCommand(
                tenant: $tenant,
                name: TenantPropertyName::fromString($name),
                value: TenantPropertyValue::fromValueType(PropertyType::STRING, $value),
                user: User::fromCoreUser($this->context->getLoggedUser()),
            )
        );
    }
}

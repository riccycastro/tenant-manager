<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Service;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

final class TenantDatabaseService
{
    private EntityManagerInterface $entityManagerMaster;
    private EntityManagerInterface $entityManager;
    private string $databasePrefix;

    public function __construct(
        ManagerRegistry $doctrine,
    ) {
        $this->entityManagerMaster = $doctrine->getManager('master'); // @phpstan-ignore-line
        $this->entityManager = $doctrine->getManager(); // @phpstan-ignore-line
        $this->databasePrefix = $_ENV['DATABASE_PREFIX'] ?? '';
    }

    /**
     * @throws \Exception
     */
    public function createTenantDatabaseUser(string $tenantCode, string $password): void
    {
        $connection = $this->entityManagerMaster->getConnection();
        $connection->executeQuery("CREATE USER `$tenantCode`@`%` IDENTIFIED BY '$password'");
        $connection->executeQuery(
            "GRANT INSERT, UPDATE, SELECT, CREATE, REFERENCES, LOCK TABLES ON {$this->databasePrefix}{$tenantCode}.* TO `$tenantCode`@`%`; FLUSH PRIVILEGES;"
        );
    }

    /**
     * @throws \Exception
     */
    public function createDatabase(string $tenantCode): void
    {
        $this->entityManagerMaster->getConnection()->executeQuery(
            "CREATE DATABASE IF NOT EXISTS {$this->databasePrefix}{$tenantCode} CHARACTER SET utf8 COLLATE utf8_general_ci;"
        );
    }

    /**
     * @throws \Exception
     */
    public function databaseExists(TenantCode $tenantCode): bool
    {
        // todo@rcastro - prepare statement
        $result = $this->entityManagerMaster->getConnection()->executeQuery(
            sprintf(
                "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '%s%s';",
                $this->databasePrefix,
                $tenantCode->toString()
            )
        );

        return (bool) $result->fetchAssociative();
    }

    public function beginDatabaseTransaction(): void
    {
        $this->entityManager->beginTransaction();
        $this->entityManagerMaster->beginTransaction();
    }

    public function commitDatabaseTransaction(): void
    {
        $this->entityManagerMaster->commit();
        $this->entityManager->commit();
    }

    public function rollbackDatabaseTransaction(): void
    {
        $this->entityManagerMaster->rollback();
        $this->entityManager->rollback();
    }
}

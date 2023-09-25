<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Service;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;

final class TenantDatabaseService
{
    private Connection $connectionMaster;
    private Connection $connectionDefault;
    private string $databasePrefix;

    public function __construct(
        ManagerRegistry $doctrine,
    ) {
        $this->connectionMaster = $doctrine->getConnection('master'); // @phpstan-ignore-line
        $this->connectionDefault = $doctrine->getConnection('default'); // @phpstan-ignore-line
        $this->databasePrefix = $_ENV['DATABASE_PREFIX'] ?? '';
    }

    /**
     * @throws \Exception
     */
    public function createTenantDatabaseUser(TenantCode $tenantCode, string $password): void
    {
        $this->connectionMaster->executeQuery(
            sprintf(
                "CREATE USER `%s`@`%%` IDENTIFIED BY '%s'",
                $tenantCode->toString(),
                $password
            )
        );
        $this->connectionMaster->executeQuery(
            sprintf(
                'GRANT INSERT, UPDATE, SELECT, CREATE, REFERENCES, LOCK TABLES ON %s%s.* TO `%s`@`%%`; FLUSH PRIVILEGES;',
                $this->databasePrefix,
                $tenantCode->toString(),
                $tenantCode->toString(),
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function createDatabase(TenantCode $tenantCode): void
    {
        $this->connectionMaster->executeQuery(
            sprintf(
                'CREATE DATABASE IF NOT EXISTS %s%s CHARACTER SET utf8 COLLATE utf8_general_ci;',
                $this->databasePrefix,
                $tenantCode->toString(),
            )
        );
    }

    /**
     * @throws \Exception
     */
    public function databaseExists(TenantCode $tenantCode): bool
    {
        // todo@rcastro - prepare statement
        $result = $this->connectionMaster->executeQuery(
            sprintf(
                "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '%s%s';",
                $this->databasePrefix,
                $tenantCode->toString()
            )
        );

        return (bool) $result->fetchAssociative();
    }

    /**
     * @throws Exception
     */
    public function beginDatabaseTransaction(): void
    {
        $this->connectionDefault->beginTransaction();
    }

    /**
     * @throws Exception
     */
    public function commitDatabaseTransaction(): void
    {
        if ($this->connectionDefault->isTransactionActive()) {
            $this->connectionDefault->commit();
        }
    }

    /**
     * @throws Exception
     */
    public function rollbackDatabaseTransaction(): void
    {
        $this->connectionDefault->rollback();
    }
}

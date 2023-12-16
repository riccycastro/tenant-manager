<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Service;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

final class TenantDatabaseService implements DatabaseServiceInterface
{
    private Connection $connectionMaster;
    private string $databasePrefix;

    public function __construct(
        ManagerRegistry $doctrine,
    ) {
        $this->connectionMaster = $doctrine->getConnection('master'); // @phpstan-ignore-line
        $this->databasePrefix = $_ENV['DATABASE_PREFIX'] ?? '';
    }

    /**
     * @throws \Exception
     */
    public function createTenantDatabaseUser(TenantCode $tenantCode, string $password): void
    {
        $this->connectionMaster->executeQuery(
            sprintf(
                "DROP USER IF EXISTS %s; CREATE USER `%s`@`%%` IDENTIFIED BY '%s'",
                $tenantCode->toString(),
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
        $databaseName = $this->generateDatabaseName($tenantCode);
        $this->connectionMaster->executeQuery(
            sprintf(
                'DROP DATABASE IF EXISTS %s; CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8 COLLATE utf8_general_ci;',
                $databaseName,
                $databaseName,
            )
        );
    }

    public function generateDatabaseName(TenantCode $code): string
    {
        return $this->databasePrefix.$code->toString();
    }

    /**
     * @throws \Exception
     */
    public function hasDatabase(TenantCode $tenantCode): bool
    {
        $result = $this->connectionMaster->executeQuery(
            sprintf(
                "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '%s%s';",
                $this->databasePrefix,
                $tenantCode->toString()
            )
        );

        return is_array($result->fetchAssociative());
    }

    /**
     * @throws \Exception
     */
    public function hasUser(TenantCode $code): bool
    {
        $result = $this->connectionMaster->executeQuery(
            sprintf(
                "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = '%s')",
                $code->toString(),
            )
        )->fetchNumeric();

        if (is_array($result)) {
            return (bool) $result[0];
        }

        return $result;
    }
}

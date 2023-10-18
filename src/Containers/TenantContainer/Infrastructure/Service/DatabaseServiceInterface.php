<?php

namespace App\Containers\TenantContainer\Infrastructure\Service;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;

interface DatabaseServiceInterface
{
    public function createTenantDatabaseUser(TenantCode $tenantCode, string $password): void;

    public function createDatabase(TenantCode $tenantCode): void;

    public function databaseExists(TenantCode $tenantCode): bool;

    public function beginDatabaseTransaction(): void;

    public function commitDatabaseTransaction(): void;

    public function rollbackDatabaseTransaction(): void;
}

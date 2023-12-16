<?php

namespace App\Containers\TenantContainer\Infrastructure\Service;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;

interface DatabaseServiceInterface
{
    public function createTenantDatabaseUser(TenantCode $tenantCode, string $password): void;

    public function createDatabase(TenantCode $tenantCode): void;

    public function generateDatabaseName(TenantCode $code): string;

    public function hasDatabase(TenantCode $tenantCode): bool;

    public function hasUser(TenantCode $code): bool;
}

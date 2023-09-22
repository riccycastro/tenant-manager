<?php

namespace App\Containers\TenantContainer\Application;

use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;

interface FindsTenantInterface
{
    public function withCode(TenantCode $code): FindsTenantInterface;

    /**
     * @return Tenant|null
     */
    public function getResult();

    /**
     * @return Tenant[]
     */
    public function getResults(): array;
}

<?php

namespace App\Containers\TenantContainer\Application;

use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;

interface FindsTenantInterface
{
    public function byCode(TenantCode $code): ?Tenant;
}

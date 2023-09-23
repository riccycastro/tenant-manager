<?php

namespace App\Containers\TenantContainer\Application;

use App\Containers\TenantContainer\Domain\Model\Tenant;

interface UpdatesTenantInterface
{
    public function save(Tenant $tenant): Tenant;
}

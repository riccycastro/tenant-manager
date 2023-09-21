<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application;

use App\Containers\TenantContainer\Domain\Model\Tenant;

interface PersistsTenantInterface
{
    public function saveAsNew(Tenant $tenant): Tenant;
}

<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\InMemory\Repository;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;

final class TenantInMemoryRepository implements PersistsTenantInterface, FindsTenantInterface
{
    /**
     * @var Tenant[]
     */
    private array $tenants = [];

    public function saveAsNew(Tenant $tenant): Tenant
    {
        $this->tenants[$tenant->getId()->toString()] = $tenant;

        return $tenant;
    }

    public function byCode(TenantCode $code): ?Tenant
    {
        foreach ($this->tenants as $tenant) {
            if ($tenant->hasSameCode($code)) {
                return $tenant;
            }
        }

        return null;
    }
}

<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\InMemory\Repository;

use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Domain\Model\Tenant;

final class TenantInMemoryRepository implements PersistsTenantInterface
{
    /**
     * @var Tenant[]
     */
    private array $tenants = []; // @phpstan-ignore-line

    public function saveAsNew(Tenant $tenant): Tenant
    {
        $this->tenants[$tenant->getId()->toString()] = $tenant;

        return $tenant;
    }
}

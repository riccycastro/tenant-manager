<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\InMemory\Repository;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Application\UpdatesTenantInterface;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\NewTenant;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Infrastructure\Data\InMemory\InMemoryRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @extends InMemoryRepository<Tenant>
 */
final class TenantInMemoryRepository extends InMemoryRepository implements PersistsTenantInterface, UpdatesTenantInterface, FindsTenantInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function saveAsNew(NewTenant $newTenant): Tenant
    {
        $tenant = $newTenant->toTenant();

        $this->entities[$tenant->getId()->toString()] = $tenant;
        $this->eventDispatcher->dispatch($newTenant->toTenantCreatedEvent());
        ++$this->count;

        return $tenant;
    }

    public function withCode(TenantCode $code): FindsTenantInterface
    {
        return $this->filter(fn (Tenant $tenant) => $tenant->hasSameCode($code));
    }

    public function withStatus(TenantStatus $tenantStatus): FindsTenantInterface
    {
        return $this->filter(fn (Tenant $tenant) => $tenant->hasStatus($tenantStatus));
    }

    public function save(Tenant $tenant): Tenant
    {
        $this->entities[$tenant->getId()->toString()] = $tenant;

        return $tenant;
    }

    public function add(Tenant $tenant): void
    {
        $this->entities[$tenant->getId()->toString()] = $tenant;
    }
}

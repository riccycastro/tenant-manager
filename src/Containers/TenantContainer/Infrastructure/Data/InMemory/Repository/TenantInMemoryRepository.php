<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\InMemory\Repository;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Application\PersistsTenantInterface;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Infrastructure\Data\InMemory\InMemoryRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @extends InMemoryRepository<Tenant>
 */
final class TenantInMemoryRepository extends InMemoryRepository implements PersistsTenantInterface, FindsTenantInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function saveAsNew(Tenant $tenant): Tenant
    {
        $this->entities[$tenant->getId()->toString()] = $tenant;
        $this->eventDispatcher->dispatch($tenant->toTenantCreatedEvent());

        return $tenant;
    }

    public function withCode(TenantCode $code): FindsTenantInterface
    {
        return $this->filter(fn (Tenant $tenant) => $tenant->hasSameCode($code));
    }
}

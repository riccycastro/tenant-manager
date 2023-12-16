<?php

namespace App\Containers\TenantContainer\Application;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Domain\Repository\Dto\ModelList;
use App\Ship\Core\Infrastructure\Exception\NonUniqueResultException;
use App\Ship\Core\Infrastructure\Exception\NoResultException;

interface FindsTenantInterface
{
    public function withCode(TenantCode $code): FindsTenantInterface;

    public function withStatus(TenantStatus $tenantStatus): FindsTenantInterface;

    /**
     * @return Tenant|null
     */
    public function getResult();

    /**
     * @return Tenant[]
     */
    public function getResults(): array;

    /**
     * @return ModelList<Tenant>
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getListResult(): ModelList;

    public function withPagination(int $page, int $itemsPerPage): static;
}

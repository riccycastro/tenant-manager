<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\QueryHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Query\FindTenantsListQuery;
use App\Ship\Core\Application\QueryHandler\QueryHandlerInterface;
use App\Ship\Core\Domain\Repository\Dto\ModelList;
use App\Ship\Core\Infrastructure\Exception\NonUniqueResultException;
use App\Ship\Core\Infrastructure\Exception\NoResultException;

final class FindTenantsListQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly FindsTenantInterface $findsTenant,
    ) {
    }

    /**
     * @return ModelList<Tenant>
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function __invoke(FindTenantsListQuery $query): ModelList
    {
        $findsTenant = $this->findsTenant;

        if (null !== $query->code) {
            $findsTenant = $findsTenant->withCode($query->code);
        }

        if (null !== $query->page && null !== $query->itemsPerPage) {
            $findsTenant = $findsTenant->withPagination($query->page, $query->itemsPerPage);
        }

        return $findsTenant->getListResult();
    }
}

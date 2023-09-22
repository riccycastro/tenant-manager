<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\QueryHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Query\FindTenantQuery;
use App\Ship\Core\Application\QueryHandler\QueryHandlerInterface;

final class FindTenantQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly FindsTenantInterface $findsTenant
    ) {
    }

    public function __invoke(FindTenantQuery $query): ?Tenant
    {
        $findsTenant = $this->findsTenant;

        if (null !== $query->code) {
            $findsTenant = $findsTenant->withCode($query->code);
        }

        return $findsTenant->getResult();
    }
}

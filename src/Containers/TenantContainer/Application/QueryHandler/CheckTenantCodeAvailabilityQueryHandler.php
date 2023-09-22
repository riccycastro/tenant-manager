<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Application\QueryHandler;

use App\Containers\TenantContainer\Application\FindsTenantInterface;
use App\Containers\TenantContainer\Domain\Query\CheckTenantCodeAvailabilityQuery;
use App\Ship\Core\Application\QueryHandler\QueryHandlerInterface;

final class CheckTenantCodeAvailabilityQueryHandler implements QueryHandlerInterface
{
    public function __construct(
        private readonly FindsTenantInterface $findsTenant
    ) {
    }

    public function __invoke(CheckTenantCodeAvailabilityQuery $command): bool
    {
        return null === $this->findsTenant->withCode($command->code)->getResult();
    }
}

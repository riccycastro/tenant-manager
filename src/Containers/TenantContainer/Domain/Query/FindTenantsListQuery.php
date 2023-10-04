<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Query;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Domain\Query\QueryInterface;

final class FindTenantsListQuery implements QueryInterface
{
    public function __construct(
        public readonly ?TenantCode $code = null,
        public readonly ?int $page = null,
        public readonly ?int $itemsPerPage = null,
    ) {
    }
}

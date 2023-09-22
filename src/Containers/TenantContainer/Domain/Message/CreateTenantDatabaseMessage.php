<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Message;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Domain\Message\AsyncMessageInterface;

final class CreateTenantDatabaseMessage implements AsyncMessageInterface
{
    public function __construct(
        public readonly TenantCode $tenantCode,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Event;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;

final class TenantCreatedEvent
{
    public function __construct(
        public readonly TenantCode $tenantCode
    ) {
    }
}

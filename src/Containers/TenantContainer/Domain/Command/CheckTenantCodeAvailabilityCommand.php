<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Command;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Domain\Command\CommandInterface;

final class CheckTenantCodeAvailabilityCommand implements CommandInterface
{
    public function __construct(
        public readonly TenantCode $code
    ) {
    }
}

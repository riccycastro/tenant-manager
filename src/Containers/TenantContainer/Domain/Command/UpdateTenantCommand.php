<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Command;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Ship\Core\Domain\Command\CommandInterface;

final class UpdateTenantCommand implements CommandInterface
{
    public function __construct(
        public readonly TenantCode $code,
        public readonly ?TenantStatus $status = null,
    ) {
    }
}

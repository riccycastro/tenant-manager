<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Command;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Ship\Core\Domain\Command\CommandInterface;

final class CreateTenantCommand implements CommandInterface
{
    public function __construct(
        public readonly TenantName $name,
        public readonly TenantCode $code,
        public readonly TenantDomainEmail $domainEmail,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Command;

use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\User;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyName;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyValue;
use App\Ship\Core\Domain\Command\CommandInterface;

final class ProcessTenantPropertyCommand implements CommandInterface
{
    public function __construct(
        public readonly Tenant $tenant,
        public readonly TenantPropertyName $name,
        public readonly TenantPropertyValue $value,
        public readonly User $user,
    ) {
    }
}

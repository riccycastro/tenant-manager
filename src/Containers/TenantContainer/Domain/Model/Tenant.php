<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Model;

use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;

final class Tenant
{
    public function __construct(
        private readonly TenantId $id,
        private readonly TenantName $name, // @phpstan-ignore-line
        private readonly TenantCode $code, // @phpstan-ignore-line
        private readonly TenantDomainEmail $domainEmail, // @phpstan-ignore-line
        private readonly User $createdBy, // @phpstan-ignore-line
    ) {
    }

    public function getId(): TenantId
    {
        return $this->id;
    }
}

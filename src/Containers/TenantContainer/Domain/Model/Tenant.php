<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Model;

use App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\UserId;

final class Tenant
{
    public function __construct(
        private readonly TenantId $id,
        private readonly TenantName $name,
        private readonly TenantCode $code,
        private readonly TenantDomainEmail $domainEmail,
        private readonly User $createdBy,
    ) {
    }

    public function getId(): TenantId
    {
        return $this->id;
    }

    public function getName(): TenantName
    {
        return $this->name;
    }

    public function getCode(): TenantCode
    {
        return $this->code;
    }

    public function getDomainEmail(): TenantDomainEmail
    {
        return $this->domainEmail;
    }

    public function getCreatedByIdentifier(): UserId
    {
        return $this->createdBy->getId();
    }

    public function hasSameCode(TenantCode $code): bool
    {
        return $this->code->isEqual($code);
    }

    public function toTenantCreatedEvent(): TenantCreatedEvent
    {
        return new TenantCreatedEvent($this->code);
    }
}

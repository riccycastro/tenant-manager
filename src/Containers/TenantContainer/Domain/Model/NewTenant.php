<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Model;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Event\TenantCreatedEvent;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;

final class NewTenant
{
    public function __construct(
        private readonly TenantId $id,
        private readonly TenantName $name,
        private readonly TenantCode $code,
        private readonly TenantDomainEmail $domainEmail,
        private readonly User $createdBy,
    ) {
    }

    public function getName(): TenantName
    {
        return $this->name;
    }

    public function getCode(): TenantCode
    {
        return $this->code;
    }

    public function getId(): TenantId
    {
        return $this->id;
    }

    public function toTenantCreatedEvent(): TenantCreatedEvent
    {
        return new TenantCreatedEvent($this->code);
    }

    public function toTenant(): Tenant
    {
        return new Tenant(
            id: $this->id,
            name: $this->name,
            code: $this->code,
            domainEmail: $this->domainEmail,
            createdBy: $this->createdBy,
            status: TenantStatus::WAITING_PROVISIONING,
            isActive: true
        );
    }

    /**
     * @return  array{
     *          id: string,
     *          name: string,
     *          code: string,
     *          domainEmail: string,
     *          createdById: string,
     *          }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name->toString(),
            'code' => $this->code->toString(),
            'domainEmail' => $this->domainEmail->toString(),
            'createdById' => $this->createdBy->getId()->toString(),
        ];
    }
}

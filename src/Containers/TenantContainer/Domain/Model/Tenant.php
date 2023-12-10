<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Model;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Exception\InvalidTenantStatusWorkFlowException;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyName;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyValue;

final class Tenant
{
    /**
     * @param TenantProperty[] $properties
     */
    public function __construct(
        private readonly TenantId $id,
        private readonly TenantName $name,
        private readonly TenantCode $code,
        private readonly TenantDomainEmail $domainEmail,
        private readonly User $createdBy,
        private readonly TenantStatus $status,
        private readonly bool $isActive,
        private readonly array $properties,
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

    public function getDomainEmail(): TenantDomainEmail
    {
        return $this->domainEmail;
    }

    public function getId(): TenantId
    {
        return $this->id;
    }

    public function getStatus(): TenantStatus
    {
        return $this->status;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function hasSameCode(TenantCode $code): bool
    {
        return $this->code->isEqual($code);
    }

    /**
     * @return TenantProperty[] $properties
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @throws InvalidTenantStatusWorkFlowException
     */
    public function setStatus(
        ?TenantStatus $status,
    ): self {
        return new self(
            $this->id,
            $this->name,
            $this->code,
            $this->domainEmail,
            $this->createdBy,
            null !== $status ? $this->statusWorkFlow($status) : $this->status,
            $this->isActive,
            $this->properties,
        );
    }

    /**
     * @throws InvalidTenantStatusWorkFlowException
     */
    private function statusWorkFlow(TenantStatus $nextStatus): TenantStatus
    {
        $statusFlow = [
            TenantStatus::WAITING_PROVISIONING->value => [TenantStatus::PROVISIONING],
            TenantStatus::PROVISIONING->value => [TenantStatus::WAITING_PROVISIONING, TenantStatus::READY_FOR_MIGRATION],
            TenantStatus::READY_FOR_MIGRATION->value => [TenantStatus::READY],
            TenantStatus::READY->value => [TenantStatus::DEACTIVATED],
            TenantStatus::DEACTIVATED->value => [TenantStatus::READY],
        ];

        if (in_array($nextStatus, $statusFlow[$this->status->value])) {
            return $nextStatus;
        }

        throw InvalidTenantStatusWorkFlowException::fromTenantStatusWorkflow($nextStatus, $this->status, $statusFlow[$this->status->value]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name->toString(),
            'code' => $this->code->toString(),
            'domainEmail' => $this->domainEmail->toString(),
            'createdBy' => $this->createdBy->toArray(),
            'status' => $this->status->value,
            'isActive' => $this->isActive,
            'properties' => array_map(fn (TenantProperty $tenantProperty) => $tenantProperty->toArray(), $this->properties),
        ];
    }

    public function hasProperty(TenantPropertyName $name): bool
    {
        foreach ($this->properties as $tenantProperty) {
            if ($tenantProperty->hasName($name)) {
                return true;
            }
        }

        return false;
    }

    public function updateProperty(TenantPropertyName $name, TenantPropertyValue $value): Tenant
    {
        $properties = [];

        foreach ($this->properties as $key => $tenantProperty) {
            if ($tenantProperty->hasName($name)) {
                $properties[] = $this->properties[$key]->update($value);
            } else {
                $properties[] = $tenantProperty;
            }
        }

        return new self(
            $this->id,
            $this->name,
            $this->code,
            $this->domainEmail,
            $this->createdBy,
            $this->status,
            $this->isActive,
            $properties,
        );
    }

    public function addProperty(TenantProperty $tenantProperty): Tenant
    {
        if ($this->hasProperty($tenantProperty->getName())) {
            return $this;
        }

        return new self(
            $this->id,
            $this->name,
            $this->code,
            $this->domainEmail,
            $this->createdBy,
            $this->status,
            $this->isActive,
            [$tenantProperty, ...$this->properties],
        );
    }
}

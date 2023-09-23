<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Model;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Exception\InvalidTenantStatusWorkFlowException;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;

final class Tenant
{
    public function __construct(
        private readonly TenantId $id,
        private readonly TenantName $name,
        private readonly TenantCode $code,
        private readonly TenantDomainEmail $domainEmail,
        private readonly User $createdBy,
        private readonly TenantStatus $status,
        private readonly bool $isActive,
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
     * @throws InvalidTenantStatusWorkFlowException
     */
    public function update(
        ?TenantStatus $status,
    ): self {
        return new self(
            $this->id,
            $this->name,
            $this->code,
            $this->domainEmail,
            $this->createdBy,
            null !== $status ? $this->statusWorkFlow($status) : $this->status,
            $this->isActive
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
            TenantStatus::READY_FOR_MIGRATION->value => [TenantStatus::PROVISIONING, TenantStatus::READY],
            TenantStatus::READY->value => [TenantStatus::DEACTIVATED],
            TenantStatus::DEACTIVATED->value => [TenantStatus::READY],
        ];

        if (in_array($nextStatus, $statusFlow[$this->status->value])) {
            return $nextStatus;
        }

        throw InvalidTenantStatusWorkFlowException::fromTenantStatusWorkflow($nextStatus, $this->status, $statusFlow[$this->status->value]);
    }
}

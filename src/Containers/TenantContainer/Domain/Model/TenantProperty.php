<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\Model;

use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyName;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyValue;

final class TenantProperty
{
    public function __construct(
        private readonly TenantPropertyId $id,
        private readonly TenantPropertyName $name,
        private readonly TenantPropertyValue $value,
        private readonly User $createdBy,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name->toString(),
            'value' => $this->value->value(),
            'type' => $this->value->type(),
            'createdBy' => $this->createdBy->toArray(),
        ];
    }

    public function getName(): TenantPropertyName
    {
        return $this->name;
    }

    public function getStringValue(): string
    {
        return $this->value->getValueString();
    }

    public function getType(): string
    {
        return $this->value->type()->value;
    }
}

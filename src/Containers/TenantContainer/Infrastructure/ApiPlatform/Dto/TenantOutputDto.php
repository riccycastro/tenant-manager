<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto;

use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\Model\TenantProperty;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use Symfony\Component\Serializer\Annotation\Groups;

final class TenantOutputDto
{
    /**
     * @param TenantPropertyOutputDto[] $properties
     */
    public function __construct(
        #[Groups([TenantResource::TENANT_READ])]
        public readonly string $name,
        #[Groups([TenantResource::TENANT_READ])]
        public readonly string $code,
        #[Groups([TenantResource::TENANT_READ])]
        public readonly bool $isActive,
        #[Groups([TenantResource::TENANT_READ])]
        public readonly string $status,
        #[Groups([TenantResource::TENANT_READ])]
        public readonly string $domainEmail,
        #[Groups([TenantResource::TENANT_READ])]
        public readonly array $properties,
    ) {
    }

    public static function fromModel(Tenant $tenant): self
    {
        return new self(
            name: $tenant->getName()->toString(),
            code: $tenant->getCode()->toString(),
            isActive: $tenant->isActive(),
            status: $tenant->getStatus()->value,
            domainEmail: $tenant->getDomainEmail()->toString(),
            properties: array_reduce($tenant->getProperties(), function (array $carry, TenantProperty $tenantProperty) {
                $carry[] = new TenantPropertyOutputDto(
                    $tenantProperty->getType(),
                    $tenantProperty->getStringValue(),
                    $tenantProperty->getName()->toString(),
                );

                return $carry;
            }, []),
        );
    }
}

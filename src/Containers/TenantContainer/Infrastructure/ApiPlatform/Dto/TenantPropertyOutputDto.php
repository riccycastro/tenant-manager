<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto;

use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use Symfony\Component\Serializer\Annotation\Groups;

final class TenantPropertyOutputDto
{
    public function __construct(
        #[Groups([TenantResource::TENANT_READ])]
        public readonly string $type,
        #[Groups([TenantResource::TENANT_READ])]
        public readonly string $value,
        #[Groups([TenantResource::TENANT_READ])]
        public readonly string $name,
    ) {
    }
}

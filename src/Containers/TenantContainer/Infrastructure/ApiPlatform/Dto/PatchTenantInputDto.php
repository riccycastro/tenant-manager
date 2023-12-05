<?php

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource\TenantResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class PatchTenantInputDto
{
    public function __construct(
        #[Assert\Choice(callback: [TenantStatus::class, 'values'], groups: ['patchValidation'])]
        #[Groups([TenantResource::TENANT_WRITE])]
        public ?string $status = null,
    ) {
    }
}

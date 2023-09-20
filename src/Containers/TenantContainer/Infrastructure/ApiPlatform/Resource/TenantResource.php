<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor\CreateTenantProcessor;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

const TENANT_READ = 'tenant_read';
const TENANT_WRITE = 'tenant_write';

#[ApiResource(
    shortName: 'Tenant',
    operations: [
        // Basic CRUD
        new Post(
            securityPostDenormalize: 'is_granted("tenant.create", object)',
            validationContext: ['groups' => ['postValidation']],
            processor: CreateTenantProcessor::class,
        ),
    ],
    normalizationContext: [
        'groups' => [TENANT_READ],
    ],
    denormalizationContext: [
        'groups' => [TENANT_WRITE],
    ]
)]
class TenantResource
{
    public function __construct(
        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\Length(min: 1, max: 255, groups: ['postValidation'])]
        #[Groups([TENANT_READ, TENANT_WRITE])]
        public ?string $name = null,

        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\NotBlank(groups: ['postValidation'])]
        #[Assert\Regex(pattern: '/^[a-zA-Z0-9_-]+$/', groups: ['postValidation'])]
        #[Groups([TENANT_WRITE])]
        public ?string $code = null,

        #[Assert\NotNull(groups: [])]
        #[Assert\Choice(choices: [true, false], groups: ['postValidation'])]
        public ?bool $isActive = null,

        public ?string $status = null,

        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\NotBlank(groups: ['postValidation'])]
        #[Assert\Regex(pattern: '/^@\S+\.\S+$/', groups: ['postValidation'], )]
        #[Groups([TENANT_WRITE])]
        public ?string $domainEmail = null,
    ) {
    }
}

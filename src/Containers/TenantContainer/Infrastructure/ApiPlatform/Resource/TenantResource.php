<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\OpenApi\TenantCodeFilter;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor\CreateTenantProcessor;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor\UpdateTenantProcessor;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Provider\TenantCollectionProvider;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Provider\TenantItemProvider;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'Tenant',
    operations: [
        // Basic CRUD
        new Get(
            uriTemplate: '/tenants/{code}',
            provider: TenantItemProvider::class,
        ),
        new GetCollection(
            filters: [TenantCodeFilter::class],
            provider: TenantCollectionProvider::class,
        ),
        new Post(
            securityPostDenormalize: 'is_granted("tenant.create", object)',
            validationContext: ['groups' => ['postValidation']],
            processor: CreateTenantProcessor::class,
        ),
        new Patch(
            uriTemplate: '/tenants/{code}',
            securityPostDenormalize: 'is_granted("tenant.update", object)',
            validationContext: ['groups' => ['patchValidation']],
            provider: TenantItemProvider::class,
            processor: UpdateTenantProcessor::class,
        ),
    ],
    normalizationContext: [
        'groups' => [TenantResource::TENANT_READ],
    ],
    denormalizationContext: [
        'groups' => [TenantResource::TENANT_WRITE],
    ]
)]
final class TenantResource
{
    public const TENANT_READ = 'tenant_read';
    public const TENANT_WRITE = 'tenant_write';

    public function __construct(
        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\Length(min: 1, max: 255, groups: ['postValidation'])]
        #[Groups([self::TENANT_READ, self::TENANT_WRITE])]
        public ?string $name = null,

        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\NotBlank(groups: ['postValidation'])]
        #[Assert\Regex(pattern: '/^[a-zA-Z0-9_-]+$/', groups: ['postValidation'])]
        #[Groups([self::TENANT_READ, self::TENANT_WRITE])]
        public ?string $code = null,

        #[Assert\NotNull(groups: [])]
        #[Assert\Choice(choices: [true, false], groups: ['postValidation'])]
        #[Groups([self::TENANT_READ])]
        public ?bool $isActive = null,

        #[Assert\Choice(callback: [TenantStatus::class, 'values'], groups: ['patchValidation'])]
        #[Groups([self::TENANT_READ, self::TENANT_WRITE])]
        public ?string $status = null,

        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\NotBlank(groups: ['postValidation'])]
        #[Assert\Regex(pattern: '/^@\S+\.\S+$/', groups: ['postValidation'], )]
        #[Groups([self::TENANT_READ, self::TENANT_WRITE])]
        public ?string $domainEmail = null,
    ) {
    }

    public static function fromModel(Tenant $tenant): self
    {
        return new self(
            $tenant->getName()->toString(),
            $tenant->getCode()->toString(),
            $tenant->isActive(),
            $tenant->getStatus()->value,
            $tenant->getDomainEmail()->toString(),
        );
    }
}

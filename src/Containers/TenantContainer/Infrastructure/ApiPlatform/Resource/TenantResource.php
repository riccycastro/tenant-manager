<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto\TenantOutputDto;
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
            denormalizationContext: [
                'groups' => [TenantResource::TENANT_WRITE],
            ],
            provider: TenantItemProvider::class,
        ),
        new GetCollection(
            denormalizationContext: [
                'groups' => [TenantResource::TENANT_WRITE],
            ],
            filters: [TenantCodeFilter::class],
            provider: TenantCollectionProvider::class,
        ),
        new Post(
            denormalizationContext: [
                'groups' => [TenantResource::TENANT_WRITE],
            ],
            securityPostDenormalize: 'is_granted("tenant.create", object)',
            validationContext: ['groups' => ['postValidation']],
            name: 'tenant_create',
            processor: CreateTenantProcessor::class,
        ),
        new Patch(
            uriTemplate: '/tenants/{code}',
            denormalizationContext: ['groups' => [TenantResource::TENANT_WRITE_PATCH]],
            securityPostDenormalize: 'is_granted("tenant.update", object)',
            validationContext: ['groups' => ['patchValidation']],
            name: 'tenant_update_patch',
            provider: TenantItemProvider::class,
            processor: UpdateTenantProcessor::class
        ),
    ],
    normalizationContext: [
        'groups' => [TenantResource::TENANT_READ],
    ],
    output: TenantOutputDto::class,
)]
final class TenantResource
{
    public const TENANT_READ = 'tenant.read';
    public const TENANT_WRITE = 'tenant.write';
    public const TENANT_WRITE_PATCH = 'tenant.write.patch';

    public function __construct(
        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\Length(min: 1, max: 255, groups: ['postValidation'])]
        #[Groups([self::TENANT_WRITE])]
        public ?string $name = null,

        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\NotBlank(groups: ['postValidation'])]
        #[Assert\Regex(pattern: '/^[a-zA-Z0-9_-]+$/', groups: ['postValidation'])]
        #[Groups([self::TENANT_WRITE])]
        public ?string $code = null,

        #[Assert\Choice(callback: [TenantStatus::class, 'values'], groups: ['patchValidation'])]
        #[Groups([self::TENANT_WRITE_PATCH])]
        public ?string $status = null,

        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\NotBlank(groups: ['postValidation'])]
        #[Assert\Regex(pattern: '/^@\S+\.\S+$/', groups: ['postValidation'])]
        #[Groups([self::TENANT_WRITE])]
        public ?string $domainEmail = null,
    ) {
    }
}

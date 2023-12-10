<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\Dto\TenantOutputDto;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\OpenApi\TenantCodeFilter;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor\CreateTenantProcessor;
use App\Containers\TenantContainer\Infrastructure\ApiPlatform\State\Processor\TenantPropertyProcessor;
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
        new Put(
            uriTemplate: '/tenants/{code}/status',
            denormalizationContext: ['groups' => [TenantResource::TENANT_WRITE_STATUS]],
            securityPostDenormalize: 'is_granted("tenant.update", object)',
            validationContext: ['groups' => ['updateStatusValidation']],
            name: 'tenant_update_status_patch',
            provider: TenantItemProvider::class,
            processor: UpdateTenantProcessor::class
        ),
        new Post(
            uriTemplate: '/tenants/{code}/properties',
            denormalizationContext: ['groups' => [TenantResource::TENANT_WRITE_PROPERTIES]],
            securityPostDenormalize: 'is_granted("tenant.update", object)',
            validationContext: ['groups' => ['postPropertiesValidation']],
            name: 'tenant_properties_post',
            provider: TenantItemProvider::class,
            processor: TenantPropertyProcessor::class
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
    public const TENANT_WRITE_STATUS = 'tenant.write.status';
    public const TENANT_WRITE_PROPERTIES = 'tenant.write.properties';

    public function __construct(
        /** @var string */
        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\Length(min: 1, max: 255, groups: ['postValidation'])]
        #[Groups([self::TENANT_WRITE])]
        public $name = null,

        /** @var string */
        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\NotBlank(groups: ['postValidation'])]
        #[Assert\Regex(pattern: '/^[a-zA-Z0-9_-]+$/', groups: ['postValidation'])]
        #[Groups([self::TENANT_WRITE])]
        public $code = null,

        /** @var string */
        #[Assert\Choice(callback: [TenantStatus::class, 'values'], groups: ['updateStatusValidation'])]
        #[Groups([self::TENANT_WRITE_STATUS])]
        public $status = null,

        /** @var string */
        #[Assert\NotNull(groups: ['postValidation'])]
        #[Assert\NotBlank(groups: ['postValidation'])]
        #[Assert\Regex(pattern: '/^@\S+\.\S+$/', groups: ['postValidation'])]
        #[Groups([self::TENANT_WRITE])]
        public $domainEmail = null,

        /** @var TenantPropertyResource[] */
        #[Assert\Valid]
        #[Groups([self::TENANT_WRITE_PROPERTIES])]
        public $properties = null,
    ) {
    }
}

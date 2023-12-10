<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\ApiPlatform\Resource;

use App\Containers\TenantContainer\Domain\Enum\PropertyType;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class TenantPropertyResource
{
    /** @var string */
    #[Assert\NotBlank(groups: ['postPropertiesValidation'])]
    #[Assert\NotNull(groups: ['postPropertiesValidation'])]
    #[Groups([TenantResource::TENANT_WRITE_PROPERTIES])]
    public $name;

    /** @var string */
    #[Assert\NotNull(groups: ['postPropertiesValidation'])]
    #[Assert\NotBlank(groups: ['postPropertiesValidation'])]
    #[Groups([TenantResource::TENANT_WRITE_PROPERTIES])]
    public $value;

    /** @var string */
    #[Assert\Choice(callback: [PropertyType::class, 'values'], groups: ['postPropertiesValidation'])]
    #[Groups([TenantResource::TENANT_WRITE_PROPERTIES])]
    public $type;
}

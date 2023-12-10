<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity;

use App\Containers\TenantContainer\Domain\Enum\PropertyType;
use App\Containers\TenantContainer\Domain\Model\TenantProperty;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyName;
use App\Containers\TenantContainer\Domain\ValueObject\TenantPropertyValue;
use Doctrine\ORM\Mapping as ORM;

/**
 * @implements ConvertsToModelInterface<TenantProperty>
 */
#[ORM\Entity]
#[ORM\Table(name: 'tenant_property')]
class TenantPropertyEntity implements ConvertsToModelInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $value;

    #[ORM\Column(type: 'string')]
    private string $type;

    #[ORM\ManyToOne(targetEntity: UserEntity::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private UserEntity $createdBy;

    #[ORM\ManyToOne(targetEntity: TenantEntity::class, inversedBy: 'tenantProperties')]
    #[ORM\JoinColumn(name: 'tenant_id', referencedColumnName: 'id')]
    private TenantEntity $tenant; // @phpstan-ignore-line

    public function __construct(
        string $id,
        string $name,
        string $value,
        string $type,
        UserEntity $createdBy,
        TenantEntity $tenant,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
        $this->createdBy = $createdBy;
        $this->tenant = $tenant;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toModel(): TenantProperty
    {
        return new TenantProperty(
            TenantPropertyId::fromString($this->id),
            TenantPropertyName::fromString($this->name),
            TenantPropertyValue::fromValueType(PropertyType::from($this->type), $this->value),
            $this->createdBy->toUser(),
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @implements ConvertsToModelInterface<Tenant>
 */
#[ORM\Entity]
#[ORM\Table(name: 'tenant')]
class TenantEntity implements ConvertsToModelInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $code;

    #[ORM\Column(type: 'string')]
    private string $domainEmail;

    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private UserEntity $createdBy;

    #[ORM\Column(type: 'string')]
    private string $status;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive;

    /** @var Collection<int, TenantPropertyEntity> */
    #[ORM\OneToMany(mappedBy: 'tenant', targetEntity: TenantPropertyEntity::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private Collection $tenantProperties;

    public function __construct()
    {
        $this->isActive = false;
        $this->status = TenantStatus::WAITING_PROVISIONING->value;
        $this->tenantProperties = new ArrayCollection();
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $entity = new self();

        $entity->id = $data['id'];
        $entity->name = $data['name'];
        $entity->code = $data['code'];
        $entity->domainEmail = $data['domainEmail'];
        $entity->createdBy = $data['createdBy'];

        return $entity;
    }

    public function update(Tenant $tenant): void
    {
        $this->name = $tenant->getName()->toString();
        $this->code = $tenant->getCode()->toString();
        $this->domainEmail = $tenant->getDomainEmail()->toString();
        $this->status = $tenant->getStatus()->value;
        $this->isActive = $tenant->isActive();
    }

    public function toModel(): Tenant
    {
        return new Tenant(
            id: TenantId::fromString($this->id),
            name: TenantName::fromString($this->name),
            code: TenantCode::fromString($this->code),
            domainEmail: TenantDomainEmail::fromString($this->domainEmail),
            createdBy: $this->createdBy->toUser(),
            status: TenantStatus::from($this->status),
            isActive: $this->isActive,
            properties: $this->tenantProperties->map(
                fn (TenantPropertyEntity $tenantPropertyEntity) => $tenantPropertyEntity->toModel()
            )->toArray(),
        );
    }
}

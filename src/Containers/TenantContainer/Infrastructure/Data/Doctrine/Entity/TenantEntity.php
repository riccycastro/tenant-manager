<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use App\Containers\TenantContainer\Domain\Model\Tenant;
use App\Containers\TenantContainer\Domain\ValueObject\TenantCode;
use App\Containers\TenantContainer\Domain\ValueObject\TenantDomainEmail;
use App\Containers\TenantContainer\Domain\ValueObject\TenantId;
use App\Containers\TenantContainer\Domain\ValueObject\TenantName;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tenant')]
class TenantEntity
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

    public function __construct()
    {
        $this->isActive = false;
        $this->status = TenantStatus::WAITING_PROVISIONING->value;
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

    public function toTenant(): Tenant
    {
        return new Tenant(
            TenantId::fromString($this->id),
            TenantName::fromString($this->name),
            TenantCode::fromString($this->code),
            TenantDomainEmail::fromString($this->domainEmail),
            $this->createdBy->toUser(),
            TenantStatus::from($this->status),
            $this->isActive
        );
    }
}

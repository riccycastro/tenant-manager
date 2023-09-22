<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity;

use App\Containers\TenantContainer\Domain\Enum\TenantStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tenant')]
class TenantEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id; // @phpstan-ignore-line

    #[ORM\Column(type: 'string')]
    private string $name; // @phpstan-ignore-line

    #[ORM\Column(type: 'string')]
    private string $code; // @phpstan-ignore-line

    #[ORM\Column(type: 'string')]
    private string $domainEmail; // @phpstan-ignore-line

    #[ORM\ManyToOne(targetEntity: UserEntity::class)]
    #[ORM\JoinColumn(name: 'created_by', referencedColumnName: 'id')]
    private UserEntity $createdBy; // @phpstan-ignore-line

    #[ORM\Column(type: 'string')]
    private string $status; // @phpstan-ignore-line

    #[ORM\Column(type: 'boolean')]
    private bool $isActive; // @phpstan-ignore-line

    public function __construct()
    {
        $this->isActive = false;
        $this->status = TenantStatus::WAITING_PROVISIONING->value;
    }

    /**
     * @param array{
     *     id: string,
     *     name: string,
     *     code: string,
     *     domainEmail: string,
     *    createdBy: UserEntity,
     * } $data
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
}

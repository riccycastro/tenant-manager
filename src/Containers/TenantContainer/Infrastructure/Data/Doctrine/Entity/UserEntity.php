<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Infrastructure\Data\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class UserEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private string $id; // @phpstan-ignore-line

    #[ORM\Column(type: 'string')]
    private string $email; // @phpstan-ignore-line
}

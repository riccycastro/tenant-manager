<?php

declare(strict_types=1);

namespace App\Containers\SecurityContainer\Infrastructure\Data\Doctrine\Entity;

use App\Containers\SecurityContainer\Domain\Model\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table(name: 'user')]
class UserEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string')]
    private ?string $id; // @phpstan-ignore-line

    #[ORM\Column(type: 'string')]
    private ?string $email; // @phpstan-ignore-line

    #[ORM\Column(type: 'string')]
    private ?string $password; // @phpstan-ignore-line

    #[ORM\Column(type: 'boolean')]
    private bool $isSystem = false; // @phpstan-ignore-line

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function toUser(): User
    {
        return new User(
            $this->id,
            $this->email,
            $this->password,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Containers\SecurityContainer\Domain\Model;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private readonly string $id,
        private readonly string $email,
        private string $password,
    ) {
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        $this->password = '';
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getId(): string
    {
        return $this->id;
    }
}

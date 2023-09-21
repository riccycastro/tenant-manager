<?php

declare(strict_types=1);

namespace App\Ship\Core\Domain\Model;

final class LoggedUser
{
    public function __construct(
        private readonly string $id,
        private readonly string $email,
    ) {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

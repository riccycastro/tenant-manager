<?php

declare(strict_types=1);

namespace App\Ship\Core\Domain\Model;

final class User
{
    public function __construct(
        private readonly int $id,
        private readonly string $email, // @phpstan-ignore-line
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}

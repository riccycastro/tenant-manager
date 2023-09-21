<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class UserEmail
{
    public readonly string $email;

    private function __construct(string $email)
    {
        Assert::email($email);

        $this->email = $email;
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }
}

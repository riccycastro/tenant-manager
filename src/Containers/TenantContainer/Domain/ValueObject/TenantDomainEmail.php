<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class TenantDomainEmail
{
    public readonly string $domainEmail;

    private function __construct(string $domainEmail)
    {
        Assert::notEmpty($domainEmail);
        Assert::regex($domainEmail, '/^@\S+\.\S+$/');

        $this->domainEmail = $domainEmail;
    }

    public static function fromString(string $domainEmail): self
    {
        return new self($domainEmail);
    }
}

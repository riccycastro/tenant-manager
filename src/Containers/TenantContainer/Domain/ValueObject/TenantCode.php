<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class TenantCode
{
    private readonly string $code;

    private function __construct(string $code)
    {
        Assert::notEmpty($code);
        Assert::regex($code, '/^[a-zA-Z0-9_-]+$/');

        $this->code = $code;
    }

    public static function fromString(string $code): self
    {
        return new self($code);
    }

    public function isEqual(TenantCode $code): bool
    {
        return $this->code === $code->code;
    }

    public function toString(): string
    {
        return $this->code;
    }
}

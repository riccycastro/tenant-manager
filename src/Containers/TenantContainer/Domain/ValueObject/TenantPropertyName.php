<?php

declare(strict_types=1);

namespace App\Containers\TenantContainer\Domain\ValueObject;

use Webmozart\Assert\Assert;

final class TenantPropertyName
{
    private readonly string $name;

    private function __construct(string $name)
    {
        Assert::notEmpty($name);
        Assert::lengthBetween($name, 1, 255);

        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        return new self($name);
    }

    public function toString(): string
    {
        return $this->name;
    }
}
